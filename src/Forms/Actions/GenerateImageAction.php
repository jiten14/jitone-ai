<?php

namespace Jiten14\JitoneAi\Forms\Actions;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Actions\Action;
use Jiten14\JitoneAi\JitoneAi;
use Illuminate\Support\Facades\Storage;

class GenerateImageAction
{
    public function execute($field, $record, $data, array $options = [])
    {
        return Action::make('generateImage')
            ->label('Generate Image with AI')
            ->icon('heroicon-s-sparkles')
            ->form([
                Textarea::make('ai_prompt')
                    ->label('Describe the image you want to generate')
                    ->required(),
                Select::make('template')
                    ->label('Or choose a template')
                    ->options(function () {
                        return app(JitoneAi::class)->getImagePrompts();
                    })
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('ai_prompt', $state);
                        }
                    }),
            ])
            ->action(function (array $data) use ($field, $options) {
                $prompt = $data['ai_prompt'] ?? null;
                
                if (empty($prompt)) {
                    throw new \Exception("Image prompt is empty or null. Form data: " . json_encode($data));
                }
                
                $imageUrl = app(JitoneAi::class)->generateImage($prompt, $options);
                
                // Convert the full URL to a relative path
                $relativePath = $this->urlToRelativePath($imageUrl);
                
                // Set the field state with an array containing the relative path
                $field->state([$relativePath]);
            })
            ->modalHeading('Generate Image with AI')
            ->modalButton('Generate');
    }

    private function urlToRelativePath($url)
    {
        $disk = config('jitone-ai.image_storage_disk', 'public');
        $path = config('jitone-ai.image_storage_path', 'ai-generated-images');
        
        // Remove the base URL to get the relative path
        $relativePath = str_replace(Storage::disk($disk)->url(''), '', $url);
        
        // Remove any leading slashes
        return ltrim($relativePath, '/');
    }
}