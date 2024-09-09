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

        // Ask to star the repo
        if ($this->confirm('Would you like to star our repo on GitHub?', true)) {
            $repoUrl = 'https://github.com/jiten14/jitone-ai';
            $this->info("Thanks! You can star the repo here: {$repoUrl}");
            
            if (PHP_OS_FAMILY === 'Darwin') {
                exec("open {$repoUrl}");
            } elseif (PHP_OS_FAMILY === 'Windows') {
                exec("start {$repoUrl}");
            } elseif (PHP_OS_FAMILY === 'Linux') {
                exec("xdg-open {$repoUrl}");
            }
        }

        return self::SUCCESS;
    }
}