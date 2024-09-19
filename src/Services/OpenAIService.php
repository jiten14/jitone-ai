<?php

namespace Jiten14\JitoneAi\Services;

use OpenAI\Laravel\Facades\OpenAI;

class OpenAIService
{
    public function generateContent(string $prompt, array $options = [])
    {
        $model = $options['model'] ?? config('jitone-ai.default_model');
        $maxTokens = $options['max_tokens'] ?? config('jitone-ai.default_max_tokens');
        $temperature = $options['temperature'] ?? config('jitone-ai.default_temperature');

        if ($model === 'gpt-3.5-turbo-instruct') {
            $completion = OpenAI::completions()->create([
                'model' => $model,
                'prompt' => $prompt,
                'max_tokens' => $maxTokens,
                'temperature' => $temperature,
            ]);

            return $completion['choices'][0]['text'];
        } else {
            $result = OpenAI::chat()->create([
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'max_tokens' => $maxTokens,
                'temperature' => $temperature,
            ]);

            return $result->choices[0]->message->content;
        }
    }
    
}