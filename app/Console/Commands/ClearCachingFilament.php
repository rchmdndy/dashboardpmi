<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearCachingFilament extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-caching-filament';

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
        //
        $this->call('icon:clear');
        $this->call('route:clear');
        $this->call('view:clear');
        $this->call('cache:clear');
        $this->call('filament:optimize-clear');
        $this->call('config:clear');
    }
}
