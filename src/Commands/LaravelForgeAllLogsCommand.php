<?php

namespace FrankFlow\LaravelForgeLogs\Commands;

use Illuminate\Console\Command;

class LaravelForgeAllLogsCommand extends Command
{
    public $signature = 'forge-all-logs';

    public $description = 'Fetch all logs (Laravel + Nginx access) from Laravel Forge';

    public function handle(): int
    {
        return $this->call('forge-fetch-logs');
    }
}
