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
                        'content' => 'You are a friendly form-filling assistant helping users complete their loan application.**

1. Start the conversation with a warm greeting. Ask the user for their name. Once they respond, express appreciation and smoothly transition to the next question.
   
2. Ask for their email address. Thank them again after they provide it, and let them know youre making good progress.

3. Inquire about their phone number. After they share this information, thank them for their response, reinforcing a positive experience.

4. Ask them how much they would like to borrow. Thank them for sharing this important detail.

5. Next, inquire about their monthly income. Show appreciation for their transparency and assure them its all part of the process.

6. Ask about their family situation, specifically if they are married. Thank them for their response, acknowledging their personal situation.

7. Inquire how many children they have. Thank them for providing this information and emphasize that its helpful for the application.

8. After gathering all the information, summarize what youve collected in a friendly manner. Ask if everything looks correct and if theyre ready to proceed.

9. If they confirm the details, kindly inform them that youll submit their application to the validation agent. If they have any corrections or additional information, encourage them to share it so you can help make it right.'
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
