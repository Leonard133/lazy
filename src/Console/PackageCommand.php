<?php

namespace Leonard133\Lazy\Console;

use Illuminate\Console\Command;

class PackageCommand extends Command
{
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
        $packages = [
            'Default Setup',
            'Laravel UI',
            'Laravel Jetstream',
            'Laravel Fortify',
            'Laravel Livewire',
            'Laravel Passport',
            'Laravel Excel',
            'Laravel Blueprint',
            'Laravel Debugbar',
            'Laravel Telescope',
            'Laravel Spatie Permission',
            'Laravel Datatable',
            'Laravel Horizon',
            'Laravel Backup',
            'Laravel Slack Notification',
            'Laravel Notification Channel',
            'Laravel Captcha',
            'Laravel QRCode',
            'Laravel Intervention Image',
            'Laravel Multi Tenancy',
            'Laravel Activity Log',
            'Tailwind CSS',
            'Tailwind UI',
            'Alpine.js'
        ];
        $choices = $this->choice('Please choose the packages you would like to install (You may use "," for multiple choice)', $packages, 0, null, true);
        dd($choices);
        return 0;
    }
}
