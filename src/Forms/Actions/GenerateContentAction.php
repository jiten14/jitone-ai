<?php

namespace Jiten14\JitoneAi\Forms\Actions;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Jiten14\JitoneAi\JitoneAi;

class GenerateContentAction
{
    public function execute($field, $record, $data, array $options = [])
    {
        return Action::make('generateContent')
            ->label('Generate with AI')
            ->icon('heroicon-s-sparkles')
            ->form([
                Toggle::make('use_existing_content')
                    ->label('Use existing content')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('ai_prompt', null);
                            $set('template', null);
                        } else {
                            $set('existing_content_action', null);
                        }
                    }),
                Select::make('existing_content_action')
                    ->label('Action on existing content')
                    ->options([
                        'refine' => 'Refine',
                        'expand' => 'Expand',
                        'shorten' => 'Shorten',
                    ])
                    ->visible(fn (callable $get) => $get('use_existing_content'))
                    ->required(fn (callable $get) => $get('use_existing_content')),
                Textarea::make('ai_prompt')
                    ->label('Enter your prompt')
                    ->required()
                    ->visible(fn (callable $get) => !$get('use_existing_content')),
                Select::make('template')
                    ->label('Or choose a template')
                    ->options(function () {
                        return app(JitoneAi::class)->getContentTemplates();
                    })
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('ai_prompt', $state);
                        }
                    })
                    ->visible(fn (callable $get) => !$get('use_existing_content')),
            ])
            ->action(function (array $data) use ($field, $options) {
                if ($data['use_existing_content']) {
                    $currentContent = $field->getState();
                    $action = $data['existing_content_action'];
                    
                    switch ($action) {
                        case 'refine':
                            $prompt = "Refine the following text: $currentContent";
                            break;
                        case 'expand':
                            $prompt = "Expand on the following text: $currentContent";
                            break;
                        case 'shorten':
                            $prompt = "Shorten the following text while maintaining its key points: $currentContent";
                            break;
                        default:
                            throw new \Exception("Invalid action selected for existing content.");
                    }
                } else {
                    $prompt = $data['ai_prompt'] ?? null;
                    
                    if (empty($prompt)) {
                        throw new \Exception("Prompt is empty or null. Form data: " . json_encode($data));
                    }
                }
                
                $generatedContent = app(JitoneAi::class)->generateContent($prompt, $options);
                
                if ($data['use_existing_content']) {
                    // Replace the existing content
                    $newContent = $generatedContent;
                } else {
                    // Append the new content to the existing content
                    $currentContent = $field->getState();
                    if ($field instanceof RichEditor) {
                        $newContent = $currentContent . "\n\n" . $generatedContent;
                    } elseif ($field instanceof Textarea) {
                        $newContent = $currentContent . "\n" . $generatedContent;
                    } else {
                        $newContent = trim($currentContent . ' ' . $generatedContent);
                    }
                }
                
                // Set the new content
                $field->state($newContent);
            })
            ->modalHeading('Generate Content with AI')
            ->modalButton('Generate');
    }
}