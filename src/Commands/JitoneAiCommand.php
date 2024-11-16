<?php

namespace Jiten14\JitoneAi\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class JitoneAiCommand extends Command
{
    public $signature = 'jitone-ai:install';

    public $description = 'Install and set up JitoneAi package';

    private $apiEndpoint = 'https://support.jiten.one/api/subscribe';

    public function handle(): int
    {
        $this->info('Setting up JitoneAi package...');

        // Publish configuration
        $this->call('vendor:publish', [
            '--provider' => "Jiten14\JitoneAi\JitoneAiServiceProvider",
            '--tag' => "jitone-ai-config"
        ]);

        //Create openai-php/laravel Config File
        $this->call('openai:install');

        // Create storage link
        $this->call('storage:link');

        // Email subscription section
        if ($this->confirm('Would you like to receive free package updates and notifications via email?', false)) {
            $this->handleEmailSubscription();
        }

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

    private function handleEmailSubscription(): void
    {
        $this->info('📧 Email Subscription Information:');
        $this->line('- Your email will only be used for package updates and important notifications');
        $this->line('- We will never share your email with third parties');
        $this->line('- You can unsubscribe at any time');
        
        $name = $this->ask('Please enter your name:');
        $email = $this->ask('Please enter your email address:');

        if (empty($email)) {
            $this->info('No email provided. Skipping subscription.');
            return;
        }

        // Validate email
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            $this->error('Invalid email format. Skipping subscription.');
            return;
        }

        // Confirm subscription with explicit consent
        if (!$this->confirm("Do you consent to receive package updates and notifications at {$email}?", true)) {
            $this->info('Subscription cancelled.');
            return;
        }

        try {
            // Send email to your API or storage system
            $response = Http::post($this->apiEndpoint, [
                'name' => $name,
                'email' => $email,
            ]);

            if ($response->successful()) {
                $this->info('✅ Successfully subscribed to package updates!');
                $this->line('You can unsubscribe at any time by visiting: https://support.jiten.one/unsubscribe');
            } else {
                $this->error('Failed to subscribe. Please try again later or contact support.');
            }
        } catch (\Exception $e) {
            $this->error('An error occurred while processing your subscription.');
        }
    }
}