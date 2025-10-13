<?php

namespace FrankFlow\LaravelForgeLogs\Commands;

use FrankFlow\LaravelForgeLogs\Services\ForgeLogService;
use Illuminate\Console\Command;

class LaravelForgeFetchAllLogsCommand extends Command
{
    public $signature = 'forge-fetch-logs';

    public $description = 'Fetch all logs (Laravel + Nginx access + Nginx error) from Laravel Forge';

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
        $this->line('1/3 Fetching Laravel application logs...');
        $laravelResult = $this->call('forge-laravel-logs');

        $this->newLine();

        // Fetch Nginx access logs
        $this->line('2/3 Fetching Nginx access logs...');
        $nginxAccessResult = $this->call('forge-nginx-access-logs');

        $this->newLine();

        // Fetch Nginx error logs
        $this->line('3/3 Fetching Nginx error logs...');
        $nginxErrorResult = $this->call('forge-nginx-error-logs');

        $this->newLine();

        if ($laravelResult === self::SUCCESS && $nginxAccessResult === self::SUCCESS && $nginxErrorResult === self::SUCCESS) {
            $this->info('All logs fetched successfully!');

            return self::SUCCESS;
        }

        $this->error('Some logs failed to fetch. Check the output above for details.');

        return self::FAILURE;
    }
}
