<?php

namespace Jiten14\JitoneAi;

use Jiten14\JitoneAi\Services\OpenAIService;
use Jiten14\JitoneAi\Services\ImageGenerationService;

class JitoneAi
{
    protected $openAIService;
    protected $imageGenerationService;

    public function __construct(OpenAIService $openAIService, ImageGenerationService $imageGenerationService)
    {
        $this->openAIService = $openAIService;
        $this->imageGenerationService = $imageGenerationService;
    }

    public function generateContent(string $prompt, array $options = [])
    {
        return $this->openAIService->generateContent($prompt, $options);
    }

    public function generateImage(string $prompt, array $options = [])
    {
        return $this->imageGenerationService->generateImage($prompt, $options);
    }

    public function getContentTemplates()
    {
        return config('jitone-ai.content_templates', []);
    }

    public function getTemplatesPlaceholders()
    {
        return config('jitone-ai.template_placeholders', []);
    }

    public function getImagePrompts()
    {
        return config('jitone-ai.image_prompts', []);
    }

    public function getPromptsPlaceholders()
    {
        return config('jitone-ai.prompt_placeholders', []);
    }
    
}