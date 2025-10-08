<?php

namespace FrankFlow\LaravelForgeLogs\Commands;

use FrankFlow\LaravelForgeLogs\Services\ForgeLogService;
use Illuminate\Console\Command;

class LaravelForgeFetchLogCommand extends Command
{
    public $signature = 'forge-fetch-log {--type=application : Type of log to fetch}';

    public $description = 'Fetch application logs from Laravel Forge';

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

        $this->info('Fetching logs from Laravel Forge...');

        $logType = $this->option('type');
        $logPath = storage_path('logs/laravel.log');

        $result = $service->fetchAndSave($logPath, $logType);

        if (! $result['success']) {
            $this->error($result['error']);

            if (isset($result['status'])) {
                $this->error("Status: {$result['status']}");
            }

            if (isset($result['body'])) {
                $this->error($result['body']);
            }

            return self::FAILURE;
        }

        $this->info("Logs fetched successfully and saved to: {$result['path']}");
        $this->line('Size: '.number_format($result['size']).' bytes');

        return self::SUCCESS;
    }
}
