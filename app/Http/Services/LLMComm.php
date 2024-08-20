<?php

namespace App\Http\Services;

use App\Models\MainBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class LLMComm {

    protected $model;
    private $threads_url;

    public function __construct($mainBrandId) {
        $brand = MainBrand::findOrFail($mainBrandId);
        $this->model = (object) json_decode(Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer '.config('app.LLM_TOKEN'),
            'Accept' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2'
        ])->get('https://api.openai.com/v1/assistants/'.$brand->chat_model));

        $this->threads_url = "https://api.openai.com/v1/threads";
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

        $run = (object) json_decode(Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer '.config('app.LLM_TOKEN'),
            'Accept' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2'
            ])->post($this->threads_url.'/'.$thread->id.'/runs', [
                'assistant_id' => $this->model->id
            ]));

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
                    ])->get($this->threads_url.'/'.$thread->id.'/runs'.'/'.$run_id, [
                        'assistant_id' => $this->model->id
                    ]));

                if($run->status == 'completed')
                    $status = true;
            }
        }

        if(!$status)
            return false;

        $messages = (object) json_decode(Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer '.config('app.LLM_TOKEN'),
            'Accept' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2'
            ])->get($this->threads_url.'/'.$thread->id.'/messages?order=desc&run_id='.$run_id, [
                'assistant_id' => $this->model->id
            ]));

        $text = $messages->data[0]->content[0]->text->value;

        $fileName = "report_{$json->week}_{$json->year}_{$json->brand_name}.txt";
        $location = "reports/{$json->brand_name}/{$fileName}";

        Storage::put($location, $text);

        return true;
    }
}