<?php

namespace Jiten14\JitoneAi\Forms\Actions;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Actions\Action;
use Jiten14\JitoneAi\JitoneAi;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

class GenerateImageAction
{
    public function execute($field, $record, $data, array $options = [])
    {
        return Action::make('generateImage')
            ->label('Generate with AI')
            ->icon('heroicon-s-sparkles')
            ->form([
                Textarea::make('ai_prompt')
                    ->label('Describe the image you want to generate')
                    ->required()
                    ->placeholder(fn ($get) => $get('ai_prompt_placeholder')),
                Select::make('template')
                    ->label('Or choose a template')
                    ->options(function () {
                        return app(JitoneAi::class)->getImagePrompts();
                    })
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $placeholders = app(JitoneAi::class)->getPromptsPlaceholders();
                        if ($state) {
                            $placeholder = $placeholders[$state] ?? 'Write your content here';
                            $set('ai_prompt_placeholder', $placeholder);
                        }
                    }),
                Placeholder::make('agencyops_ad')
                    ->label('')
                    ->content(new \Illuminate\Support\HtmlString(
                        '<div style="margin-top:4px; padding:8px 12px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; display:flex; align-items:center; justify-content:space-between; font-size:12px; color:#6b7280;">'
                        . '<span>🚀 Running an agency? <a href="https://introdoor.com" target="_blank" style="color:#6366f1; font-weight:600; text-decoration:none;">AgencyOps</a> — self-hosted agency dashboard. $299 once, yours forever.</span>'
                        . '<a href="https://introdoor.com" target="_blank" style="margin-left:12px; white-space:nowrap; font-size:11px; color:#6366f1; font-weight:500; text-decoration:none;">Learn more →</a>'
                        . '</div>'
                    )),
            ])
            ->action(function (array $data) use ($field, $options) {
                if (empty(config('openai.api_key'))) {
                    Notification::make()
                        ->warning()
                        ->title('OpenAI API Key Missing')
                        ->body('Please add your OpenAI API Key to the .env file before proceeding.')
                        ->send();
                    return;
                }

                try {
                    $prompt = $data['ai_prompt'] ?? null;
                    
                    if (empty($prompt)) {
                        throw new \Exception("Image prompt is empty or null. Form data: " . json_encode($data));
                    }
                    
                    $imageUrl = app(JitoneAi::class)->generateImage($prompt, $options);
                    
                    // Convert the full URL to a relative path
                    $relativePath = $this->urlToRelativePath($imageUrl);
                    
                    // Set the field state with an array containing the relative path
                    $field->state([$relativePath]);

                    // Notify the user of successful image generation
                    Notification::make()
                        ->success()
                        ->title('Image Generated Successfully')
                        ->body('The AI-generated image has been added to the field.')
                        ->send();

                } catch (\Exception $e) {
                    // Notify the user if an error occurs
                    Notification::make()
                        ->danger()
                        ->title('Error Generating Image')
                        ->body('An error occurred while generating the image: ' . $e->getMessage())
                        ->send();
                }
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