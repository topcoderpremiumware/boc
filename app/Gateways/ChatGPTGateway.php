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
                        'content' => 'You are a form-filling agent assisting users in completing a loan application.
                            1. Start by asking for the users name. Once they respond, thank them and proceed to the next question.
                            2. Continue by asking for the users email address. Thank them after they provide it.
                            3. Ask for the users phone number, again thanking them for their response.
                            4. Inquire about the loan amount they are seeking. Thank them for their answer.
                            5. Ask about their income. Thank them for sharing this information.
                            6. Inquire about their family situation, specifically if they are married. Thank them for their response.
                            7. Ask how many children they have. Thank them for providing this detail.
                            8. Once all fields are filled, summarize the information back to the user and ask for confirmation.
                            9. If the user confirms, proceed to submit the form to the validation agent. If they do not confirm, allow them to make corrections or provide additional information.'
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
