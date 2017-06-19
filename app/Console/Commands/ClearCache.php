<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Artisan;
use Cache;

class ClearCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clearcache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all cache of website';

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
     * @return mixed
     */
    public function handle()
    {
        // Reset cache
        Cache::flush();
        // Reset OP cache
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        // Clear cache in Artisan
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        // Delete all files in views folder
        $cached_views_directory = app('path.storage') . '/views/';
        $files = glob($cached_views_directory . '*');
        foreach($files as $file) {
            if(is_file($file)) {
                @unlink($file);
            }
        }

        $this->info('All cache cleared.');
    }
}
