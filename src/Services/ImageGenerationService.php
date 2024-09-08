<?php

namespace Jiten14\JitoneAi\Services;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Exception;

class ImageGenerationService
{
    public function generateImage(string $prompt, array $options = [])
    {
        try {
            $size = $options['size'] ?? config('jitone-ai.default_image_size');
            $model = config('jitone-ai.image_model');

            $result = OpenAI::images()->create([
                'model' => $model,
                'prompt' => $prompt,
                'n' => 1,
                'size' => $size,
                'response_format' => 'url',
            ]);

            $imageUrl = $result['data'][0]['url'] ?? null;

            if (!$imageUrl) {
                throw new Exception("Failed to generate image URL.");
            }

            // Download the image content
            $client = new Client();
            $response = $client->get($imageUrl);
            $imageContent = $response->getBody()->getContents();

            if (empty($imageContent)) {
                throw new Exception("Failed to download image content.");
            }

            // Save the image locally
            $fileName = 'ai_generated_' . Str::random(10) . '.png';
            $disk = config('jitone-ai.image_storage_disk', 'public');
            $path = config('jitone-ai.image_storage_path', 'ai-generated-images');
            
            $fullPath = $path . '/' . $fileName;
            
            if (!Storage::disk($disk)->put($fullPath, $imageContent)) {
                throw new Exception("Failed to save the image locally.");
            }

            return Storage::disk($disk)->url($fullPath);
        } catch (Exception $e) {
            // Log the error
            \Log::error('Image generation failed: ' . $e->getMessage());
            
            // Rethrow the exception with a user-friendly message
            throw new Exception("Failed to generate and save the image. Please try again.");
        }
    }
}