<?php

namespace Jiten14\JitoneAi;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Jiten14\JitoneAi\Commands\JitoneAiCommand;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Jiten14\JitoneAi\Forms\Actions\GenerateContentAction;
use Jiten14\JitoneAi\Services\OpenAIService;
use Jiten14\JitoneAi\Services\ImageGenerationService;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Illuminate\Support\Facades\Artisan;

class JitoneAiServiceProvider extends PackageServiceProvider
{
    public static string $name = 'jitone-ai';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasCommand(JitoneAiCommand::class);
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

        $this->checkDependencies();
    }

    protected function registerWithAIMacro(string $componentClass)
    {
        $componentClass::macro('withAI', function (array $options = []) {
            return $this->hintAction(
                app(GenerateContentAction::class)->execute($this, null, [], $options)
            );
        });
    }

    protected function checkDependencies(): void
    {
        $requiredPackages = [
            'openai-php/laravel' => '^0.8.1|^0.10.0|^0.13.0',
            'spatie/laravel-package-tools' => '^1.15.0',
            'filament/filament' => '^3.2',
            'filament/forms' => '^3.0',
        ];

        $missingOrOutdated = [];

        foreach ($requiredPackages as $package => $version) {
            if (!$this->isPackageInstalled($package)) {
                $missingOrOutdated[] = "{$package} (not installed, requires {$version})";
            } elseif ($this->needsUpgrade($package, $version)) {
                $installedVersion = $this->getInstalledVersion($package);
                $missingOrOutdated[] = "{$package} (installed: {$installedVersion}, requires {$version})";
            }
        }
    }

    protected function isPackageInstalled(string $package): bool
    {
        $installedPackages = json_decode(file_get_contents(base_path('composer.json')), true)['require'] ?? [];
        return isset($installedPackages[$package]);
    }

    protected function needsUpgrade(string $package, string $requiredVersion): bool
    {
        $installedVersion = $this->getInstalledVersion($package);
        $versions = explode('|', trim($requiredVersion, '^~'));
        foreach ($versions as $version) {
            if (version_compare($installedVersion, trim($version, '^~'), '>=')) {
                return false;
            }
        }
        return true;
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
}