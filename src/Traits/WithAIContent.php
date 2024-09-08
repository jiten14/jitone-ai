<?php

namespace Jiten14\JitoneAi\Traits;

use Filament\Forms\Components\Actions\Action;
use Jiten14\JitoneAi\Forms\Actions\GenerateContentAction;

trait WithAIContent
{
    public function withAI(array $options = [])
    {
        $this->hintAction(
            app(GenerateContentAction::class)->execute($this, null, [], $options)
        );

        return $this;
    }
}