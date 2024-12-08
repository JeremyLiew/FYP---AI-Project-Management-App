<?php


namespace App\Http\Controllers\Web;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OpenAIController extends Controller
{
    public function sendMessage(Request $request)
    {
        $client = new Client();

        // OpenAI API key and endpoint
        $openai_api_key = env('OPENAI_API_KEY');
        $api_url = 'https://api.openai.com/v1/chat/completions';

        try {
            // Make the request to OpenAI's API
            $response = $client->post($api_url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $openai_api_key,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo', // OpenAI model
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                        ['role' => 'user', 'content' => $request->message], // The message sent from Vue.js
                    ],
                ],
            ]);

            // Decode the response
            $data = json_decode($response->getBody(), true);
            $gpt_reply = $data['choices'][0]['message']['content'];

            // Return the reply from GPT-3.5 to the Vue.js frontend
            return response()->json(['message' => $gpt_reply]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }
}
