<?php

namespace FrankFlow\LaravelForgeLogs\Commands;

use FrankFlow\LaravelForgeLogs\Services\ForgeLogService;
use Illuminate\Console\Command;

use function Laravel\Prompts\select;

class LaravelForgeDeleteLogsCommand extends Command
{
    public $signature = 'forge-delete-logs {--force : Skip confirmation prompt}';

    public $description = 'Delete logs on Laravel Forge';

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

        // Show interactive menu to select log type
        $type = select(
            'Which logs would you like to delete?',
            [
                'site' => 'Site/Application Logs',
                'nginx-access' => 'Nginx Access Logs',
                'nginx-error' => 'Nginx Error Logs',
                'all' => 'All Logs',
            ]
        );

        // Show confirmation unless --force flag is used
        if (! $this->option('force')) {
            $this->warn("This will delete the {$this->getTypeDescription($type)}.");
            $confirmed = $this->confirm('Are you sure you want to continue?', false);

            if (! $confirmed) {
                $this->info('Operation cancelled.');

                return self::SUCCESS;
            }
        }

        $this->info('Deleting logs from Laravel Forge...');

        $result = $this->deleteLogsByType($service, $type);

        if (! $result['success']) {
            $this->error($result['error']);

            if (isset($result['status'])) {
                $this->error("Status: {$result['status']}");
            }

            if (isset($result['body'])) {
                $this->error($result['body']);
            }

            if (isset($result['results'])) {
                $this->line('');
                $this->info('Detailed Results:');
                foreach ($result['results'] as $logType => $logResult) {
                    $status = $logResult['success'] ? '✓' : '✗';
                    $this->line("  {$status} {$logType}: ".($logResult['success'] ? 'Deleted' : $logResult['error']));
                }
            }

            return self::FAILURE;
        }

        if ($type === 'all') {
            $this->info('All logs deleted successfully from Laravel Forge!');
            $this->line('');
            $this->info('Deleted logs:');
            foreach ($result['results'] as $logType => $logResult) {
                $this->line("  ✓ {$logType}");
            }
        } else {
            $this->info("{$this->getTypeDescription($type)} deleted successfully from Laravel Forge!");
        }

        return self::SUCCESS;
    }

    private function deleteLogsByType(ForgeLogService $service, string $type): array
    {
        return match ($type) {
            'site' => $service->deleteSiteLog(),
            'nginx-access' => $service->deleteNginxAccessLog(),
            'nginx-error' => $service->deleteNginxErrorLog(),
            'all' => $service->deleteAllLogs(),
            default => [
                'success' => false,
                'error' => "Unknown log type: {$type}",
            ],
        };
    }

    private function getTypeDescription(string $type): string
    {
        return match ($type) {
            'site' => 'site/application logs',
            'nginx-access' => 'nginx access logs',
            'nginx-error' => 'nginx error logs',
            'all' => 'all logs',
            default => 'logs',
        };
    }
}
