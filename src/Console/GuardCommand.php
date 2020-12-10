<?php

namespace Leonard133\Lazy\Console;

use Illuminate\Console\Command;

class GuardCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lazy:guard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup multiple guards';

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
        $guards = $this->ask('Please enter guards need to added seperate with ","');
        if(empty($guards))
        {
            $this->error('Please enter guards need to be added');
            return 0;
        }
        $guardsArr = explode(',', $guards);
        $changed = "";
        $authConfig = \Config::get('auth');
        foreach ($guardsArr as $key => $guard)
        {
            $ucfirstGuard = ucfirst($guard);
            $pluralGuard = \Str::plural($guard);
            $file = file(app_path('Providers/RouteServiceProvider.php'));
            if(!in_array("->prefix('$guard')", array_map('trim', $file)))
            {
                // Route Service Provider Text
                $changed .= <<<EOT
\n            Route::middleware('web')
                ->namespace(\$this->namespace)
                ->prefix('$guard')
                ->group(base_path('routes/$guard.php'));\n
EOT;

                // Add Guard Config
                if(!array_key_exists($guard,$authConfig['guards']))
                {
                    $authConfig['guards'][$guard] = [
                        'driver' => 'session',
                        'provider' => $pluralGuard,
                    ];
                }
                if(empty($authConfig['providers'][$pluralGuard]))
                {
                    $authConfig['providers'][$pluralGuard] = [
                        'driver' => 'eloquent',
                        'model' => "App\\Models\\".$ucfirstGuard,
                    ];
                }

                // create Model file
                copy(base_path('vendor/leonard133/lazy/stubs/models/model.stub'), app_path("Models/".ucfirst($guard).".php"));
                file_put_contents(app_path("Models/".ucfirst($guard).".php"), str_replace(
                        '{{ model }}',
                        ucfirst($guard),
                        file_get_contents(app_path('Models/' . ucfirst($guard) . '.php')))
                );

                // Create Route file
                copy(base_path('vendor/leonard133/lazy/stubs/routes/web.stub'), base_path("routes/$guard.php"));
                file_put_contents(base_path("routes/$guard.php"), str_replace(
                        '{{ type }}',
                        $guard,
                        file_get_contents(base_path("routes/$guard.php")))
                );

                // Copy migration file
                if($guard !== 'user')
                {
                    copy(database_path('migrations/2014_10_12_000000_create_users_table.php'), database_path('migrations/2014_10_12_000000_create_'.$pluralGuard.'_table.php'));
                    file_put_contents(database_path('migrations/2014_10_12_000000_create_'.$pluralGuard.'_table.php'), str_replace(['users','CreateUsersTable'], [$pluralGuard,'Create'.ucfirst($pluralGuard).'Table'], file_get_contents(database_path('migrations/2014_10_12_000000_create_'.$pluralGuard.'_table.php'))));
                }

            }
        }
        // Overwrite RouteServiceProvider
        array_splice($file, 48, 0, $changed);
        $file_content = implode('', $file);
        file_put_contents(app_path('Providers/RouteServiceProvider.php'), $file_content);
        file_put_contents(app_path('Providers/RouteServiceProvider.php'), str_replace(
                "public const HOME = '/home';",
                "public const HOME = '/';",
                file_get_contents(app_path('Providers/RouteServiceProvider.php')))
        );
        $finalAuth = $this->varexport($authConfig, 1);
        file_put_contents(base_path('config/auth.php'), "<?php\n return $finalAuth ;");
        \Artisan::call('optimize');
        $this->info('Guards for '. $guards. ' has been added');
        return 0;
    }

    function varexport($expression, $return = FALSE)
    {
        $export = var_export($expression, TRUE);
        $export = preg_replace('/^([ ]*)(.*)/m', '$1$1$2', $export);
        $array = preg_split("/\r\n|\n|\r/", $export);
        $array = preg_replace(['/\s*array\s\($/', '/\)(,)?$/', '/\s=>\s$/'], [NULL, ']$1', ' => ['], $array);
        $export = join(PHP_EOL, array_filter(['['] + $array));
        if ((bool)$return) return $export; else echo $export;
    }
}
