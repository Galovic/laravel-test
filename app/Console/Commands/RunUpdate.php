<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Artisan;
use Cache;

class RunUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:run {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run update';

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
        $name = $this->argument('name');

        if (!$name) {
            $name = $this->ask('What update you want to run?');
        }

        $updator = $this->getUpdator($name);

        $this->line('Running update ' . $name . '...');
        $updator->run();
        $this->info('Done!');
        return true;
    }

    /**
     * Get a seeder instance from the container.
     *
     * @param string $name - Update name
     * @return \Illuminate\Database\Seeder
     */
    protected function getUpdator($name)
    {
        $class = $this->laravel->make($name);

        return $class->setContainer($this->laravel);
    }
}
