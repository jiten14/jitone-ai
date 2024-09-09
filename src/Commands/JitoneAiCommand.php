<?php

namespace Jiten14\JitoneAi\Commands;

use Illuminate\Console\Command;

class JitoneAiCommand extends Command
{
    public $signature = 'jitone-ai:install';

    public $description = 'Install and set up JitoneAi package';

    public function handle(): int
    {
        $this->info('Setting up JitoneAi package...');

        // Publish configuration
        $this->call('vendor:publish', [
            '--provider' => "Jiten14\JitoneAi\JitoneAiServiceProvider",
            '--tag' => "jitone-ai-config"
        ]);

        // Create storage link
        $this->call('storage:link');

        $this->info('JitoneAi package has been set up successfully!');
        $this->info('Please review the configuration file at config/jitone-ai.php');

        return self::SUCCESS;
    }
}