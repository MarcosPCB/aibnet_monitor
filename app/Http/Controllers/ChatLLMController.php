<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Chat;
use App\Http\Services\LLMComm;
use Log;
use Storage;

class ChatLLMController extends Controller
{
    public function create(Request $request) {
        $request->validate([
            'text' => 'required|string',
            'main_brand_id' => 'required|exists:main_brand,id',
            'name' => 'string'
        ]);

        $json = [];
        $json[] = (object) [
            'who' => 'user',
            'text'=> $request->text,
        ];


        $chat = Chat::create([
            'text' => json_encode($json),
            'main_brand_id' => $request->main_brand_id,
            'name' => $request->name
        ]);

        return response()->json($chat, 201);
    }

    public function createAndRun(Request $request) {
        $request->validate([
            'text' => 'required|string',
            'main_brand_id' => 'required|exists:main_brand,id',
            'name' => 'string'
        ]);

        $llm = new LLMComm($request->main_brand_id);

        $data = $llm->createThread($request->text);

        $json = [];
        $json[] = (object) [
            'who' => 'user',
            'text'=> $request->text,
        ];

        $chat = Chat::create([
            'text' => json_encode($json),
            'thread_id' => $data->thread->id,
            'main_brand_id' => $request->main_brand_id,
            'name' => $request->name
        ]);

        return response()->stream(function () use ($data, $json, $chat, $llm) {
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer '.config('app.LLM_TOKEN'),
                'Accept' => 'application/json',
                'OpenAI-Beta' => 'assistants=v2'
                ])->withOptions([
                    'stream' => true,
                    'timeout' => 0
                ])->post($data->url.'/'.$data->thread->id.'/runs', [
                    'assistant_id' => $data->model->id,
                    'stream' => true
                ]);

            $text = [
                'who' => 'assistant',
                'text'=> '',
            ];

            if ($response->successful()) {
                // Lendo o stream em chunks
                $body = $response->getBody();
                $text = [
                    'who' => 'assistant',
                    'text'=> '',
                ];
    
                $stream = '';

                echo 'API_THREAD_ID:'.$chat->id.';\n';
                ob_flush();
                flush();
    
                // Lendo o stream em chunks
                $body = $response->getBody();
                while (!$body->eof()) {  // Usando eof() no lugar de feof()
                    $chunk = $body->read(1024);  // Lendo o stream em pedaços de 1024 bytes
                    echo $chunk;  // Enviando o chunk para o cliente
                    $stream .= $chunk;
                    ob_flush();   // Despejando o buffer para o cliente
                    flush();      // Garantindo que o conteúdo seja enviado imediatamente
                }

                $streamObj = $llm->processString($stream);

                foreach($streamObj as $e) {
                    if($e->event == "thread.message.delta") {
                        if($e->data->delta['content'][0]['type'] == 'image_file') {
                            $file_id = $e->data->delta['content'][0]['image_file']['file_id'];
                            $result = Http::withoutVerifying()->withHeaders([
                                'Authorization' => 'Bearer '.config('app.LLM_TOKEN'),
                                'Accept' => 'application/json',
                                'OpenAI-Beta' => 'assistants=v2'
                                ])->get("https://api.openai.com/v1/files/".$file_id.'/content');

                            // Verifique se a requisição foi bem-sucedida
                            if ($result->successful()) {
                                // Pegue o conteúdo do arquivo
                                $fileContent = $result->body();

                                // Defina o nome do arquivo (você pode personalizar o nome como desejar)
                                $fileName = $file_id.'.png';

                                // Salve o arquivo na pasta 'storage/app/tmp'
                                Storage::disk('public')->put('tmp/'.$fileName, $fileContent);

                                $fileUrl = Storage::url('tmp/'.$fileName);

                                $text['text'] .= '![image]('.$fileUrl.')';
                            } else {
                                Log::error('Unable to retrieve image file '.$file_id);
                            }

                            if($e->data->delta['content'][1]['type'] == 'text')
                                $text['text'] .= $e->data->delta['content'][1]['text']['value'];
                        } else if($e->data->delta['content'][0]['type'] == 'text')
                            $text['text'] .= $e->data->delta['content'][0]['text']['value'];
                    }
                }

                $json[] = $text;

                $chat->text = json_encode($json);
                $chat->save();
            } else {
                // Em caso de erro na resposta
                echo "Erro ao conectar com a API OpenAI";
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',  // Tipo de conteúdo para SSE
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive'
        ]);
        
    }

    public function addTextToThread(Request $request, $id) {
        $request->validate([
            'text' => 'required|string'
        ]);

        $chat = Chat::findOrFail($id);

        $json = json_decode($chat->text);

        $json[] = [
            'who' => 'user',
            'text'=> $request->text,
        ];

        $chat->update([
            'text' => $json
        ]);

        $llm = new LLMComm($chat->main_brand_id);

        $result = $llm->addMessage($request->text, $chat->thread_id);

        if(!$result)
            return response()->json('Unable to add message to thread', 500);

        $chat->save();

        return response()->stream(function () use ($chat, $json, $llm) {
            $mainBrand = $chat->mainBrand()->first();
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer '.config('app.LLM_TOKEN'),
                'Accept' => 'application/json',
                'OpenAI-Beta' => 'assistants=v2'
                ])->withOptions([
                    'stream' => true,
                    'timeout' => 0
                ])->post("https://api.openai.com/v1/threads/".$chat->thread_id.'/runs', [
                    'assistant_id' => $mainBrand->chat_model,
                    'stream' => true
                ]);

            $text = [
                'who' => 'assistant',
                'text'=> '',
            ];

            $stream = '';

            if ($response->successful()) {
                // Lendo o stream em chunks
                $body = $response->getBody();
                while (!$body->eof()) {  // Usando eof() no lugar de feof()
                    $chunk = $body->read(1024);  // Lendo o stream em pedaços de 1024 bytes
                    echo $chunk;  // Enviando o chunk para o cliente
                    $stream .= $chunk;
                    ob_flush();   // Despejando o buffer para o cliente
                    flush();      // Garantindo que o conteúdo seja enviado imediatamente
                }

                $streamObj = $llm->processString($stream);

                foreach($streamObj as $e) {
                    if($e->event == "thread.message.delta") {
                        if($e->data->delta['content'][0]['type'] == 'image_file') {
                            $file_id = $e->data->delta['content'][0]['image_file']['file_id'];
                            $result = Http::withoutVerifying()->withHeaders([
                                'Authorization' => 'Bearer '.config('app.LLM_TOKEN'),
                                'Accept' => 'application/json',
                                'OpenAI-Beta' => 'assistants=v2'
                                ])->get("https://api.openai.com/v1/files/".$file_id.'/content');

                            // Verifique se a requisição foi bem-sucedida
                            if ($result->successful()) {
                                // Pegue o conteúdo do arquivo
                                $fileContent = $result->body();

                                // Defina o nome do arquivo (você pode personalizar o nome como desejar)
                                $fileName = $file_id.'.png';

                                // Salve o arquivo na pasta 'storage/app/tmp'
                                Storage::disk('public')->put('tmp/'.$fileName, $fileContent);

                                $fileUrl = Storage::url('tmp/'.$fileName);

                                $text['text'] .= '![image]('.$fileUrl.')';
                            } else {
                                Log::error('Unable to retrieve image file '.$file_id);
                            }

                            if($e->data->delta['content'][1]['type'] == 'text')
                                $text['text'] .= $e->data->delta['content'][1]['text']['value'];
                        } else if($e->data->delta['content'][0]['type'] == 'text')
                            $text['text'] .= $e->data->delta['content'][0]['text']['value'];
                    }
                }

                $json[] = $text;

                $chat->text = json_encode($json);
                $chat->save();
            } else {
                // Em caso de erro na resposta
                echo "Erro ao conectar com a API OpenAI";
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',  // Tipo de conteúdo para SSE
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive'
        ]);
    }

    public function attachThread(Request $request, $id) {
        $request->validate([
            'thread_id' => 'required|string'
        ]);

        $chat = Chat::findOrFail($id);

        $chat->update([
            'thread_id' => $request->thread_id
        ]);

        $chat->save();

        return response()->json($chat, 200);
    }

    public function addText(Request $request, $id) {
        $request->validate([
            'text' => 'required|string',
            'who' => 'required|string'
        ]);

        $chat = Chat::findOrFail($id);

        $json = (object) json_decode($chat->text);

        $json[] = (object) [
            'who' => $request->who,
            'text'=> $request->text,
        ];

        $chat->update([
            'text' => $json
        ]);

        $chat->save();

        return response()->json($chat, 200);
    }

    public function getChat($id) {
        $chat = Chat::findOrFail($id);
        return response()->json(json_decode($chat), 200);
    }

    public function listAll($mainBrandId) {
        $chats = Chat::where('main_brand_id', $mainBrandId)->get();
        return response()->json($chats, 200);
    }

    public function renameChat(Request $request, $id) {
        $request->validate([
            'name' => 'required|string'
        ]);

        $chat = Chat::findOrFail($id);

        $chat->update([
            'name' => $request->name
        ]);

        $chat->save();

        return response()->json($chat, 200);
    }

    public function delete(Request $request) {
        $chat = Chat::findOrFail($request->id);

        $chat->delete();

        return response()->json(['message' => 'Chat deleted successfully'], 200);
    }
}
