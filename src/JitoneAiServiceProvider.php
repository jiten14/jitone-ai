<?php

namespace Jiten14\JitoneAi;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Jiten14\JitoneAi\Commands\JitoneAiCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Jiten14\JitoneAi\Forms\Actions\GenerateContentAction;
use Jiten14\JitoneAi\Services\OpenAIService;
use Jiten14\JitoneAi\Services\ImageGenerationService;
use Spatie\LaravelPackageTools\Commands\InstallCommand;

class JitoneAiServiceProvider extends PackageServiceProvider
{
    public static string $name = 'jitone-ai';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasCommand(JitoneAiCommand::class)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('jiten14/jitone-ai');
            });
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(JitoneAi::class, function ($app) {
            return new JitoneAi(
                $app->make(OpenAIService::class),
                $app->make(ImageGenerationService::class)
            );
        });
    }

    public function packageBooted(): void
    {
        $this->registerWithAIMacro(TextInput::class);
        $this->registerWithAIMacro(Textarea::class);
        $this->registerWithAIMacro(RichEditor::class);

        $this->checkAndInstallDependencies();
    }

    protected function registerWithAIMacro(string $componentClass)
    {
        $componentClass::macro('withAI', function (array $options = []) {
            return $this->hintAction(
                app(GenerateContentAction::class)->execute($this, null, [], $options)
            );
        });
    }

    protected function checkAndInstallDependencies(): void
    {
        $requiredPackages = [
            'openai-php/laravel' => '^0.8.1',
            'spatie/laravel-package-tools' => '^1.15.0',
        ];

        foreach ($requiredPackages as $package => $version) {
            if (!$this->isPackageInstalled($package)) {
                $this->installPackage($package, $version);
            } elseif ($package === 'openai-php/laravel' && $this->needsUpgrade($package, $version)) {
                $this->upgradePackage($package, $version);
            }
        }

        $this->checkOtherRequirements();
    }

    protected function isPackageInstalled(string $package): bool
    {
        $installedPackages = json_decode(file_get_contents(base_path('composer.json')), true)['require'] ?? [];
        return isset($installedPackages[$package]);
    }

    protected function needsUpgrade(string $package, string $requiredVersion): bool
    {
        $installedVersion = $this->getInstalledVersion($package);
        return version_compare($installedVersion, trim($requiredVersion, '^~'), '<');
    }

    protected function getInstalledVersion(string $package): string
    {
        $composerLock = json_decode(file_get_contents(base_path('composer.lock')), true);
        foreach ($composerLock['packages'] as $installedPackage) {
            if ($installedPackage['name'] === $package) {
                return $installedPackage['version'];
            }
        }
        return '0.0.0';
    }

    protected function installPackage(string $package, string $version): void
    {
        Log::info("Installing {$package}...");
        Artisan::call('composer require ' . $package . ':' . $version);
    }

    protected function upgradePackage(string $package, string $version): void
    {
        Log::info("Upgrading {$package}...");
        Artisan::call('composer require ' . $package . ':' . $version);
    }

    protected function checkOtherRequirements(): void
    {
        $requiredPackages = [
            'filament/filament' => '^3.2',
            'filament/forms' => '^3.0',
        ];

        $missingPackages = [];

        foreach ($requiredPackages as $package => $version) {
            if (!$this->isPackageInstalled($package)) {
                $missingPackages[] = $package . ':' . $version;
            }
        }

        if (!empty($missingPackages)) {
            Log::error('The following packages are required but not installed:');
            foreach ($missingPackages as $package) {
                Log::error('- ' . $package);
            }
            Log::error('Please install these packages before proceeding.');
            // We don't call exit(1) here as it would stop the entire application
        }
    }
}