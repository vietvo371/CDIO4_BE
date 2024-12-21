<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ChatbotController extends Controller
{
    public function handleChat(Request $request)
    {
        $message = $request->input('message');

        // Gửi yêu cầu đến OpenAI API
        $client = new Client();
        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-3.5-turbo', // hoặc gpt-3.5-turbo
                'messages' => [
                    ['role' => 'user', 'content' => $message]
                ],
                'max_tokens' => 200,
            ],
        ]);

        $result = json_decode($response->getBody(), true);

        return response()->json([
            'message' => $result['choices'][0]['message']['content'] ?? 'Không thể trả lời câu hỏi này.',
        ]);
    }
}
