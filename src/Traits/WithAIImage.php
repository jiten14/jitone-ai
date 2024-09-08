<?php

namespace Jiten14\JitoneAi\Traits;

use Filament\Forms\Components\Actions\Action;
use Jiten14\JitoneAi\Forms\Actions\GenerateImageAction;

trait WithAIImage
{
    public function imageAI(array $options = [])
    {
        $this->hintAction(
            fn ($component) => app(GenerateImageAction::class)->execute($component, null, [], $options)
        );

        return $this;
    }
}