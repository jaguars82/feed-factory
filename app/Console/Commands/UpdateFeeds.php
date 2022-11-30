<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateFeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feeds:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all the active feeds';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo 'выполняем обновление...';
        return Command::SUCCESS;
    }
}
