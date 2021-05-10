<?php

namespace Leonard133\Lazy\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\ProgressBar;

class PackageCommand extends Command
{

    public $packages = [];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lazy:packages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Laravel common packages';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->packages = [
            'Default Setup',
            'Laravel UI',
            'Jetstream',
            'Fortify',
            'Livewire',
            'Passport',
            'Excel',
            'Blueprint',
            'Debugbar',
            'Telescope',
            'Spatie Permission',
            'Datatable',
            'Horizon',
            'Backup',
            'Slack Notification',
            'Notification Channel',
            'Captcha',
            'QRCode',
            'Intervention Image',
            'Multi Tenancy',
            'Activity Log',
            'Tailwind CSS',
            'Tailwind UI',
            'Alpine.js'
        ];
        $choices = $this->choice('Please choose the packages you would like to install (You may use "," for multiple choice)', $this->packages, 0, null, true);
        if (count($choices) > 0) {
            $extra = 3;
            ProgressBar::setFormatDefinition('custom', ' %current%/%max% [%bar%] %message%');
            $bar = $this->output->createProgressBar(count($choices) + $extra);
            $bar->setOverwrite(false);
            $bar->setFormat('custom');
            $bar->setMessage('Starting...');
            $bar->start();

            $this->progressPackage($bar, $choices);

            $bar->setMessage('All packages complete installed. Happy Coding~');
            $bar->finish();

            $this->callSilent('migrate');
            exec('composer dump-autoload -o --quiet');
            $this->callSilent('cache:clear');
            $this->callSilent('config:clear');
            $this->callSilent('route:clear');
            $this->newLine();
        }
        return 0;
    }

    public function progressPackage($bar, $choices)
    {
        if (in_array('Default Setup', $choices))
            $this->defaultSetup($bar);
        if (in_array('Debugbar', $choices))
            $this->setupDebugbar($bar);
        if (in_array('Telescope', $choices))
            $this->setupTelescope($bar);
    }

    public function setupDebugbar($bar)
    {
        if (!file_exists(config_path('debugbar.php'))) {
            sleep(1);
            $bar->setMessage('Installing debugbar...');
            exec('composer require barryvdh/laravel-debugbar --prefer-stable --dev --quiet');
            $bar->advance();
            sleep(1);
            $bar->setMessage('Configuring debugbar...');
            copy(base_path('vendor/barryvdh/laravel-debugbar/config/debugbar.php'), config_path('debugbar.php'));
            file_put_contents(config_path('debugbar.php'), str_replace('false, // Display Laravel authentication status', 'true, // Display Laravel authentication status', file_get_contents(config_path('debugbar.php'))));
            $bar->advance();
            sleep(1);
            $bar->setMessage('Debugbar setup completed');
            $bar->advance();
            sleep(1);
        } elseif (file_exists(config_path('debugbar.php'))) {
            $bar->setMessage('Debugbar has been installed. Skipping debugbar setup');
            $bar->advance(3);
            sleep(1);
        }
    }

    public function setupTelescope($bar)
    {
        if (!file_exists(config_path('telescope.php'))) {
            sleep(1);
            $bar->setMessage('Installing telescope...');
            exec('composer require laravel/telescope --prefer-stable --dev --quiet');
            $bar->advance();
            sleep(1);
            $bar->setMessage('Configuring telescope...');
            copy(base_path('vendor/laravel/telescope/config/telescope.php'), config_path('telescope.php'));
            \File::copyDirectory(base_path('vendor/laravel/telescope/public'), public_path('vendor/telescope'));
            copy(base_path('vendor/leonard133/lazy/stubs/providers/TelescopeServiceProvider.stub'), app_path('Providers/TelescopeServiceProvider.php'));
            $this->registerTelescopeServiceProvider();
            $bar->advance();
            sleep(1);
            $bar->setMessage('Telescope setup completed');
            $bar->advance();
            sleep(1);
        } elseif (file_exists(config_path('telescope.php'))) {
            $bar->setMessage('Telescope has been installed. Skipping debugbar setup');
            $bar->advance(3);
            sleep(1);
        }
    }

    public function defaultSetup($bar)
    {
        $default = config('lazy.defaultPackages');
    }

    protected function registerTelescopeServiceProvider()
    {
        $namespace = \Str::replaceLast('\\', '', $this->laravel->getNamespace());

        $appConfig = file_get_contents(config_path('app.php'));

        if (\Str::contains($appConfig, $namespace . '\\Providers\\TelescopeServiceProvider::class')) {
            return;
        }

        $lineEndingCount = [
            "\r\n" => substr_count($appConfig, "\r\n"),
            "\r" => substr_count($appConfig, "\r"),
            "\n" => substr_count($appConfig, "\n"),
        ];

        $eol = array_keys($lineEndingCount, max($lineEndingCount))[0];

        file_put_contents(config_path('app.php'), str_replace(
            "{$namespace}\\Providers\RouteServiceProvider::class," . $eol,
            "{$namespace}\\Providers\RouteServiceProvider::class," . $eol . "        {$namespace}\Providers\TelescopeServiceProvider::class," . $eol,
            $appConfig
        ));

        file_put_contents(app_path('Providers/TelescopeServiceProvider.php'), str_replace(
            'namespace App\Providers;',
            "namespace {$namespace}\Providers;",
            file_get_contents(app_path('Providers/TelescopeServiceProvider.php'))
        ));

        $guards = array_keys(config('auth.guards'));
        if(in_array('admin', $guards))
        {
            file_put_contents(app_path('Providers/TelescopeServiceProvider.php'), str_replace(
                '{{ guard }}',
                "!auth()->guard('admin')->guest()",
                file_get_contents(app_path('Providers/TelescopeServiceProvider.php'))
            ));
        } else {
            file_put_contents(app_path('Providers/TelescopeServiceProvider.php'), str_replace(
                '{{ guard }}',
                "!auth()->guest()",
                file_get_contents(app_path('Providers/TelescopeServiceProvider.php'))
            ));
        }
    }
}
