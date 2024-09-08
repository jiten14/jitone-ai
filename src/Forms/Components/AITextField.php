<?php

namespace Jiten14\JitoneAi\Forms\Components;

use Filament\Forms\Components\RichEditor;
use Jiten14\JitoneAi\Traits\WithAIContent;
use Filament\Forms\Components\Actions\Action;

class AITextField extends RichEditor
{
    use WithAIContent;
}