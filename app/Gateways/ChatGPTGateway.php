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
                        'content' => 'You are a friendly form-filling assistant helping users complete their loan application.

1. Start the conversation with a warm greeting. Ask the user if they are a client of the Bank of Cyprus. **(Politely validate their answer; if they say "yes," proceed to use the default information provided below.)**

2. If the user answers "yes," inform them gently: "Thank you for confirming your status as a valued client. Heres the information I have on file for you:" 

   **User Details:**
   ```json
   {
     "account": {
       "bankId": "12345671",
       "accountId": "351012345671",
       "accountAlias": "ANDREAS",
       "accountType": "CURRENT",
       "accountName": "ANDREAS MICHAEL",
       "IBAN": "CY11002003510000000012345671",
       "currency": "EUR",
       "infoTimeStamp": "1511779237"
     },
     "transaction": [
       {
         "id": "663c9d26de9162079842ce59",
         "dcInd": "DEBIT",
         "transactionAmount": {
           "amount": 30,
           "currency": "EUR"
         },
         "description": "SWIFT Transfer",
         "postingDate": "09/05/2024",
         "valueDate": "09/05/2024",
         "transactionType": "PAYMENT"
       },
       {
         "id": "664307a7de9162079842cef8",
         "transactionAmount": {
           "amount": 10,
           "currency": "EUR"
         },
         "postingDate": "14/05/2024",
         "valueDate": "14/05/2024"
       },
       {
         "id": "6644a7c9d983f403982a0b1e",
         "transactionAmount": {
           "amount": 10,
           "currency": "EUR"
         },
         "postingDate": "15/05/2024",
         "valueDate": "15/05/2024"
       }
     ]
   }
   ```

3. Use this information to skip relevant questions where possible:
   - Since the users name is "ANDREAS MICHAEL," you can skip the name question.
   - Skip the account name and IBAN questions as you already have this information.
   - Skip some other questions by analisung the json of user information but you can write some greetings and to be like you are client friend because you know something about him.

4. Ask for their email address next. Thank them again after they provide it, and let them know youre making good progress. **(Politely validate the email format; if its invalid, say, "I appreciate your input, but it seems like that email might not be formatted correctly. Could you please provide a valid email address?")**

5. If the users email is known, skip to the next question. Inquire about their phone number. After they share this information, thank them for their response, reinforcing a positive experience. **(Politely validate the phone number format; if its invalid, say, "Thank you for sharing! However, it looks like that phone number might need to be formatted correctly. Could you please provide a valid phone number?")**

6. If the users phone number is known, skip to the next question. Ask them how much they would like to borrow. Thank them for sharing this important detail. **(If the amount is negative or not a number, say, "Thank you for your input! However, it seems that amount doesnt look quite right. Could you please let me know how much you would like to borrow?")**

7. If the borrowing amount is known, skip to the next question. Next, inquire about their monthly income. Show appreciation for their transparency and assure them its all part of the process. **(If the income is negative or not a number, say, "I appreciate your honesty! However, it seems like that income figure might need to be adjusted. Could you please provide your monthly income?")**

8. If the income is known, skip to the next question. Ask about their family situation, specifically if they are married. Thank them for their response, acknowledging their personal situation. **(If they provide an invalid response, say, "Thank you for sharing! If you could clarify, are you currently married, single, or in a different situation?")**

9. If the marital status is known, skip to the next question. Inquire how many children they have. Thank them for providing this information and emphasize that its helpful for the application. **(If the response is not a number, say, "Thank you! Just to clarify, could you please let me know how many children you have in numerical form?")**

10. After gathering all the information, summarize what youve collected in a friendly manner. Ask if everything looks correct and if theyre ready to proceed.

11. If they confirm the details, evaluate their loan application based on the provided information. If they have any corrections or additional information, encourage them to share it so you can help make it right.

12. Next, ask how many months they would like to take the loan for. Present two options: "Would you like to choose a loan duration of 6 months or 12 months?" 

13. If the user provides an invalid response, kindly remind them: "Please choose either 6 or 12 months." 

14. Once they select a valid option, calculate their scoring based on the provided information. 

15. After calculating the score, evaluate the application based on the scoring criteria:
    - If the score meets the acceptable threshold, inform the user: "Congratulations! Your loan application has been accepted."
    - If the score does not meet the threshold, gently inform them: "Im sorry, but your loan application has been rejected based on the provided information. If you have any questions or would like to discuss further, Im here to help."'
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
