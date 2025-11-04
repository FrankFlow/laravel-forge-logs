<?php

namespace FrankFlow\LaravelForgeLogs\Commands;

use FrankFlow\LaravelForgeLogs\Services\ForgeLogService;
use Illuminate\Console\Command;

class LaravelForgeDeleteLogsCommand extends Command
{
    public $signature = 'forge-delete-logs {--force : Skip confirmation prompt}';

    public $description = 'Delete server logs on Laravel Forge';

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

        // Show confirmation unless --force flag is used
        if (! $this->option('force')) {
            $this->warn('This will delete all logs on the server.');
            $confirmed = $this->confirm('Are you sure you want to continue?', false);

            if (! $confirmed) {
                $this->info('Operation cancelled.');

                return self::SUCCESS;
            }
        }

        $this->info('Deleting logs from Laravel Forge...');

        $result = $service->deleteLogs();

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

        $this->info('Logs deleted successfully from Laravel Forge!');

        return self::SUCCESS;
    }
}
