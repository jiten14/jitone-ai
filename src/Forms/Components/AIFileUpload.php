<?php

namespace Jiten14\JitoneAi\Forms\Components;

use Filament\Forms\Components\FileUpload;
use Jiten14\JitoneAi\Traits\WithAIImage;

class AIFileUpload extends FileUpload
{
    use WithAIImage;
}