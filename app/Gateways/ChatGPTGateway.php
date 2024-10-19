<?php

namespace App\Gateways;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use LogicException;
use Tectalic\OpenAi\Authentication;
use Tectalic\OpenAi\ClientException;
use Tectalic\OpenAi\Manager;
use Tectalic\OpenAi\Models\ChatCompletions\CreateResponse;
use Tectalic\OpenAi\Models\ChatCompletions\CreateRequest as ChatCreateRequest;

class ChatGPTGateway
{
    static function answer($id,$text): string|bool
    {
        $auth = new Authentication(env('CHAT_GPT_API'));
        $httpClient = new \GuzzleHttp\Client();

        try{
            $client = Manager::access();
        }catch (LogicException $e) {
            $client = Manager::build($httpClient, $auth);
        }


        if(Cache::has($id)){
            $cache = Cache::get($id);
            $cache[] = [
                'role' => 'user',
                'content' => $text
            ];
        }else{
            $cache = [[
                'role' => 'user',
                'content' => $text
            ]];
        }
        Cache::put($id,$cache,3600);

            /** @var CreateResponse $response */
        $request = $client->chatCompletions()->create(
            new ChatCreateRequest([
                'user' => 'user-'.$id,
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a bank employee, you advise people on banking issues. For example, issuing a loan or buying goods with payment in installments.'
                    ],
                    ...$cache
                ],
            ])
        );

        try {
            $response = $request->toModel();
            $answer = $response->choices[0]->message->content;

            $cache[] = [
                'role' => 'assistant',
                'content' => $answer
            ];
            Cache::put($id,$cache,3600);

            return $answer;
        } catch (ClientException $e) {
            Log::info('ChatGPTGateway::answer has error {error}', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
