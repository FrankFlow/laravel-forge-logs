<?php

namespace FrankFlow\LaravelForgeLogs\Commands;

use FrankFlow\LaravelForgeLogs\Services\ForgeLogService;
use Illuminate\Console\Command;

class LaravelForgeFetchAllLogsCommand extends Command
{
    public $signature = 'forge-fetch-logs';

    public $description = 'Fetch all logs (Laravel + Nginx access) from Laravel Forge';

    public function handle(): int
    {
        $service = ForgeLogService::fromConfig();

        // Validate configuration
        $errors = $service->validateConfig();
        if (! empty($errors)) {
            foreach ($errors as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $this->info('Fetching all logs from Laravel Forge...');
        $this->newLine();

        // Fetch Laravel logs
        $this->line('1/2 Fetching Laravel application logs...');
        $laravelResult = $this->call('forge-laravel-logs');

        $this->newLine();

        // Fetch Nginx access logs
        $this->line('2/2 Fetching Nginx access logs...');
        $nginxResult = $this->call('forge-nginx-logs');

        $this->newLine();

        if ($laravelResult === self::SUCCESS && $nginxResult === self::SUCCESS) {
            $this->info('All logs fetched successfully!');

            return self::SUCCESS;
        }

        $this->error('Some logs failed to fetch. Check the output above for details.');

        return self::FAILURE;
    }
}
