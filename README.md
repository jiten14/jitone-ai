
# Jitone AI:- Build AI-powered Filament forms.

**jitone-ai is a powerful FilamentPHP plugin that integrates AI-powered features directly into your Filament forms.**

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jiten14/jitone-ai.svg?style=flat-square)](https://packagist.org/packages/jiten14/jitone-ai)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jiten14/jitone-ai/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jiten14/jitone-ai/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jiten14/jitone-ai.svg?style=flat-square)](https://packagist.org/packages/jiten14/jitone-ai)

## What's New in Jitone AI v0.1.1

1. **Support for Multiple OpenAI Endpoints**:
   - The latest OpenAI models, including the `gpt-4` and `gpt-3.5-turbo`, now use the **Chat API endpoint**, improving performance and response flexibility.
   - The `gpt-3.5-turbo-instruct` model will continue using the **Completion API endpoint**.

2. **Completion Models Now Considered Legacy**:
   - Please note that the models using the Completion endpoint, such as `gpt-3.5-turbo-instruct`, are now considered **legacy models**.
   - We highly recommend trying out the latest models for improved results and future-proofing. You can explore them here: [OpenAI Models Documentation](https://platform.openai.com/docs/models).

## Installation

```bash
composer require jiten14/jitone-ai
```
## Usage
1. After downloading, Run the package installation command:

```bash
php artisan jitone-ai:install
```

**This command will:**
- Publish the configuration file for `Jitone AI` Settings.
- Publish the configiration file for `openai-php/laravel` Settings.
- Create a symbolic link for storage

2. We use [openai-php/laravel](https://github.com/openai-php/laravel) package to connect with OpenAI API. Blank environment variables for the OpenAI API key and organization id are already appended to your .env file, Add your API key here.

```env
OPENAI_API_KEY=sk-...
OPENAI_ORGANIZATION=org-...
```
## Configuration

The `config/jitone-ai.php` file allows you to set default values and templates:

* Set default AI models, max tokens, and temperature
* Configure image generation settings
* Add custom content templates

## Usage for Developers

### Adding AI to Built-in Filament Form Fields

You can add AI generation capabilities to `TextInput`, `Textarea`, and `RichEditor` fields using the `withAI()` method:

#### Without Options

```php
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;

TextInput::make('title')
    ->withAI()

Textarea::make('description')
    ->withAI()

RichEditor::make('content')
    ->withAI()
```

#### With Options

```php
TextInput::make('title')
    ->withAI([
        'model' => 'gpt-4',
        'max_tokens' => 100,
        'temperature' => 0.7,
    ])

Textarea::make('description')
    ->withAI([
        'model' => 'gpt-3.5-turbo',
        'max_tokens' => 200,
        'temperature' => 0.5,
    ])

RichEditor::make('content')
    ->withAI([
        'model' => 'gpt-4',
        'max_tokens' => 500,
        'temperature' => 0.8,
    ])
```

### Using Dedicated AI Fields

#### AITextField

The package provides a dedicated `AITextField` for enhanced content generation:

##### Without Options:

```php
use Jiten14\JitoneAi\Forms\Components\AITextField;

AITextField::make('content')->withAI()
```

##### With Options:

```php
AITextField::make('content')
    ->withAI([
        'model' => 'gpt-4',
        'max_tokens' => 300,
        'temperature' => 0.6,
    ])
```

#### AIFileUpload for Image Generation

Use the `AIFileUpload` field with the `imageAI()` method to enable AI image generation:

##### Without Options:

```php
use Jiten14\JitoneAi\Forms\Components\AIFileUpload;

AIFileUpload::make('image')
    ->imageAI()
```

##### With Options:

```php
AIFileUpload::make('image')
    ->imageAI([
        'size' => '1024x1024',
    ])
```

## Usage for End-Users

### Generating Content

1. In a form with AI-enabled fields, users will see a "Generate with AI" link right upper to the field.
2. Clicking this link opens a modal where users can:
   * Enter a custom prompt
   * Choose from pre-defined templates (if configured)
   * Use existing content for modification
3. If modifying existing content, users can choose to:
   * Refine: Improve the existing text
   * Expand: Add more details to the existing text
   * Shorten: Summarize the existing text
4. After entering the prompt, selecting a template, or choosing a modification option, click "Generate" to create or modify the content.
5. The generated or modified content will be inserted into the form field.

### Generating Images

1. For fields with AI image generation, users will see a "Generate with AI" link right upper to the upload field.
2. Clicking this link opens a modal where users can:
   * Describe the image they want to generate
   * Choose from pre-defined image prompts (if configured)
   * Select the desired image size (if multiple options are provided)
3. After entering the description, selecting a template, and choosing the size, click "Generate" to create the image.
4. The generated image will be uploaded and associated with the form field.

## Advanced Features

* The package automatically checks for required dependencies and their versions, logging warnings if any are missing or outdated.
* Developers can add custom templates and prompts through the configuration file.

## Changelog

Jitone AI follows semantic versioning:

- **v0.1.0**: Initial release.
- **v0.1.1**: Added support for multiple OpenAI endpoints.
- **v0.1.3**: Added placeholders to Ai Promt Fields.

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## FAQ
1. **File upload previews not loading after generating Image.**
    - **Ans.**- Make sure that the APP_URL variable in your .env file matches the domain you're using to access your app from, including the protocol (http or https).

## Security Vulnerabilities

If you discover any security vulnerabilities or bugs, please let us know so I can address them promptly. 

## Support

For support with this package or to report any issues, feel free to reach out [Jitendriya Tripathy](mailto:jitendriya14@gmail.com). I am happy to assist you!

## Credits

- [Jitendriya Tripathy](https://github.com/jiten14)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.