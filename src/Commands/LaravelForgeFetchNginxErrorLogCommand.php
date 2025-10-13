<?php

namespace FrankFlow\LaravelForgeLogs\Commands;

use FrankFlow\LaravelForgeLogs\Services\ForgeLogService;
use Illuminate\Console\Command;

class LaravelForgeFetchNginxErrorLogCommand extends Command
{
    public $signature = 'forge-nginx-error-logs';

    public $description = 'Fetch nginx error logs from Laravel Forge';

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

        $this->info('Fetching nginx error logs from Laravel Forge...');

        $logPath = storage_path(config('forge-logs.log_paths.nginx_error', 'logs/nginx/error.log'));

        $result = $service->fetchAndSaveNginxErrorLogs($logPath);

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

        $this->info("Nginx error logs fetched successfully and saved to: {$result['path']}");
        $this->line('Size: '.number_format($result['size']).' bytes');

        return self::SUCCESS;
    }
}
