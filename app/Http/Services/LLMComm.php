<?php

namespace App\Http\Services;

use App\Models\MainBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class LLMComm {

    protected $model;
    private $threads_url;
    private $vector_url;
    private $upload_url;
    private $file_url;

    public function __construct($mainBrandId) {
        $brand = MainBrand::findOrFail($mainBrandId);
        $this->model = (object) json_decode(Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer '.config('app.LLM_TOKEN'),
            'Accept' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2'
        ])->get('https://api.openai.com/v1/assistants/'.$brand->chat_model));

        $this->threads_url = "https://api.openai.com/v1/threads";
        $this->vector_url = "https://api.openai.com/v1/vector_stores";
        $this->upload_url = "https://api.openai.com/v1/uploads";
        $this->file_url = "https://api.openai.com/v1/files";
    }

    public function generateReport($json) {
        $jsonString = json_encode($json);
        $thread = (object) json_decode(Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer '.config('app.LLM_TOKEN'),
            'Accept' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2'
            ])->post($this->threads_url, [
                'messages' => [[
                    'role' => 'user',
                    'content' => "Preciso que você, com muito amor e carinho, gere um relatório para o cliente {$json->brand_name} baseado
                        nestes dados em formato JSON que vou te mandar. Estes dados se referem a postagens feitas
                        em redes sociais na semana atual. Peço que, por favor, você gere este relatório
                        com muita atenção e carinho, pois o cliente iria amar. Obrigado. Segue o JSON:
                        {$jsonString}
                        
                        Por favor, siga a estrutura que você considerar melhor, confio em você. Caso dê tudo certo,
                        apenas responda com o relatório e nada mais."
                ]]
            ]));

        if(isset($thread->error))
            return null;

        $run = (object) json_decode(Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer '.config('app.LLM_TOKEN'),
            'Accept' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2'
            ])->post($this->threads_url.'/'.$thread->id.'/runs', [
                'assistant_id' => $this->model->id
            ]));

        if(isset($runs->error))
            return null;

        $status = false;

        if($run->status == 'completed')
            $status = true;
        else {
            $run_id = $run->id;
            $startTime = time();
            $timeout = 30;

            while(!$status) {
                // Tempo excedido!
                if (time() - $startTime > $timeout)
                    break;
                
                $run = (object) json_decode(Http::withoutVerifying()->withHeaders([
                    'Authorization' => 'Bearer '.config('app.LLM_TOKEN'),
                    'Accept' => 'application/json',
                    'OpenAI-Beta' => 'assistants=v2'
                    ])->get($this->threads_url.'/'.$thread->id.'/runs'.'/'.$run_id));

                if(isset($run->error))
                    return null;

                if($run->status == 'completed')
                    $status = true;
            }
        }

        if(!$status)
            return null;

        $messages = (object) json_decode(Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer '.config('app.LLM_TOKEN'),
            'Accept' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2'
            ])->get($this->threads_url.'/'.$thread->id.'/messages?order=desc&run_id='.$run_id));

        if(isset($messages->error))
            return null;

        $text = $messages->data[0]->content[0]->text->value;

        $fileName = "report_{$json->week}_{$json->year}_{$json->brand_name}.txt";
        $location = "reports/{$json->brand_name}/{$fileName}";

        Storage::put($location, $text);

        return $fileName;
    }

    private function getMimeType($fileExtension)
    {
        switch ($fileExtension) {
            case '.c':
                return 'text/x-c';
            case '.cs':
                return 'text/x-csharp';
            case '.cpp':
                return 'text/x-c++';
            case '.doc':
                return 'application/msword';
            case '.docx':
                return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            case '.html':
                return 'text/html';
            case '.java':
                return 'text/x-java';
            case '.json':
                return 'application/json';
            case '.md':
                return 'text/markdown';
            case '.pdf':
                return 'application/pdf';
            case '.php':
                return 'text/x-php';
            case '.pptx':
                return 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
            case '.py':
                return 'text/x-python'; // Geralmente, apenas um dos valores é necessário para ".py"
            case '.rb':
                return 'text/x-ruby';
            case '.tex':
                return 'text/x-tex';
            case '.txt':
                return 'text/plain';
            case '.css':
                return 'text/css';
            case '.js':
                return 'text/javascript';
            case '.sh':
                return 'application/x-sh';
            case '.ts':
                return 'application/typescript';
            default:
                return 'application/octet-stream'; // MIME type padrão caso a extensão não seja reconhecida
        }
    }
    

    private function uploadFile($location, $fileName) {
        if (!Storage::exists($location))
            return null;

        if(Storage::size($location) > 64 * 1024 * 1024)
            return null;

        $ext = pathinfo(Storage::path($location), PATHINFO_EXTENSION);

        $mime = $this->getMimeType($ext);

        $upload = (object) json_decode(Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer '.config('app.LLM_TOKEN')
            ])->post($this->upload_url, [
                'purpose' => 'assistants',
                'filename' => $fileName,
                'bytes' => Storage::size($location),
                'mime_type' => $mime
            ]));

        //$md5 = Storage::md5($location);

        if(isset($upload->error))
            return null;

        $stream = Storage::readStream($location);

        if ($stream === false)
            return null;

        $part = (object) json_decode(Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer '.config('app.LLM_TOKEN'),
            ])->asMultipart()->attach('data', Storage::get($location), $fileName)
            ->post($this->upload_url.'/'.$upload->id.'/parts'));

         if(isset($part->error))
            return null;

        $complete = (object) json_decode(Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer '.config('app.LLM_TOKEN')
            ])->post($this->upload_url.'/'.$upload->id.'/complete', [
                'part_ids' => [ $part->id ]
                //'md5' => $md5
            ]));

        if(isset($complete->error))
            return null;

        if($complete->status == 'completed')
            return $complete;

        return null;
    }

    public function storeReport($brand_name, $fileName) {
        $location = "reports/{$brand_name}/{$fileName}";

        if (!Storage::exists($location))
            return null;
        
        $has_more = true;
        $vector = null;
        $after = null;

        while($has_more) {
            $vectors = null;
            if($after) {
                $vectors = (object) json_decode(Http::withoutVerifying()->withHeaders([
                    'Authorization' => 'Bearer '.config('app.LLM_TOKEN'),
                    'Accept' => 'application/json',
                    'OpenAI-Beta' => 'assistants=v2'
                    ])->get($this->vector_url.'?limit=100&after='.$after));

                if(isset($vectors->error))
                    return null;
            } else {
                $vectors = (object) json_decode(Http::withoutVerifying()->withHeaders([
                    'Authorization' => 'Bearer '.config('app.LLM_TOKEN'),
                    'Accept' => 'application/json',
                    'OpenAI-Beta' => 'assistants=v2'
                    ])->get($this->vector_url.'?limit=100'));

                if(isset($vectors->error))
                    return null;
            }

            for($i = 0; $i < count($vectors->data); $i++) {
                $v = $vectors->data[$i];

                if($v->name == 'reports_'.$brand_name.'_KL') {
                    $vector = $v;
                    break;
                }
            }

            if($vector)
                break;

            if($vectors->has_more) {
                $has_more = true;
                $after = $vectors->last_id;
            } else
                break;
        }

        if(!$vector)
            return null;

        $file = $this->uploadFile($location, $fileName);

        if(!$file)
            return null;

        $vector_file = (object) json_decode(Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer '.config('app.LLM_TOKEN'),
            'Accept' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2'
            ])->post($this->vector_url.'/'.$vector->id.'/files', [
                'file_id' => $file->file->id
            ]));

        if(isset($vector_file->error))
            return null;

        if($vector_file->status != 'cancelled' && $vector_file->status != 'failed')
            return true;

        return null;
    }

    public function createThread($text) {
        $thread = (object) json_decode(Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer '.config('app.LLM_TOKEN'),
            'Accept' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2'
            ])->post($this->threads_url, [
                'messages' => [[
                    'role' => 'user',
                    'content' => $text
                ]]
            ]));

        if(isset($thread->error))
            return null;

        $data = new \stdClass();
        $data->url = $this->threads_url;
        $data->model = $this->model;
        $data->thread = $thread;

        return $data;
    }

    public function addMessage($text, $thread_id) {
        $thread = (object) json_decode(Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer '.config('app.LLM_TOKEN'),
            'Accept' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2'
            ])->post($this->threads_url.'/'.$thread_id.'/messages', [
                'role' => 'user',
                'content' => $text
            ]));

        print_r($thread);

        if(isset($thread->error))
            return null;

        return true;
    }

    public function processString($input) {
        // Separando os eventos e dados por linhas
        $lines = explode("\n", $input);
        $events = [];
        $currentEvent = null;
    
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
    
            // Identificando eventos
            if (strpos($line, 'event:') === 0) {
                $currentEvent = trim(substr($line, 6));
            }
    
            // Identificando dados
            if (strpos($line, 'data:') === 0) {
                $data = trim(substr($line, 5));
                if ($currentEvent) {
                    // Convertendo o JSON em objeto
                    $events[] = (object) [
                        'event' => $currentEvent,
                        'data' => (object) json_decode($data, true)
                    ];
                }
                $currentEvent = null;
            }
        }
    
        return $events;
    }
}