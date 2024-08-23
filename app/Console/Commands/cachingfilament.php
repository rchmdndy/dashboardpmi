<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class cachingfilament extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:caching-filament';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('icon:cache');
        $this->call('filament:cache-components');
        $this->call('route:cache');
        $this->call('view:cache');
        $this->call('filament:optimize');
        $this->call('config:cache');
    }
}
