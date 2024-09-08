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

        $completion = OpenAI::completions()->create([
            'model' => $model,
            'prompt' => $prompt,
            'max_tokens' => $maxTokens,
            'temperature' => $temperature,
        ]);

        return $completion['choices'][0]['text'];
    }
}