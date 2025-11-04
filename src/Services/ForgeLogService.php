<?php

namespace FrankFlow\LaravelForgeLogs\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ForgeLogService
{
    private ?string $token;

    private ?string $organization;

    private ?int $serverId;

    private ?int $siteId;

    public function __construct(
        ?string $token,
        ?string $organization,
        ?int $serverId,
        ?int $siteId
    ) {
        $this->token = $token;
        $this->organization = $organization;
        $this->serverId = $serverId;
        $this->siteId = $siteId;
    }

    /**
     * Create service instance from config
     */
    public static function fromConfig(): self
    {
        return new self(
            config('forge-logs.forge_token'),
            config('forge-logs.forge_organization'),
            config('forge-logs.forge_server_id'),
            config('forge-logs.forge_site_id')
        );
    }

    /**
     * Fetch application logs from Laravel Forge
     */
    public function fetchLogs(string $logType = 'application'): Response
    {
        $url = $this->buildLogUrl($logType);

        return Http::withToken($this->token ?? '')
            ->acceptJson()
            ->get($url);
    }

    /**
     * Extract log content from API response
     */
    public function extractContent(Response $response): ?string
    {
        if ($response->failed()) {
            return null;
        }

        $data = $response->json();

        return $data['data']['attributes']['content'] ?? null;
    }

    /**
     * Save log content to file
     */
    public function saveToFile(string $content, string $filePath): bool
    {
        $directory = dirname($filePath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        return file_put_contents($filePath, $content) !== false;
    }

    /**
     * Fetch and save logs in one operation
     */
    public function fetchAndSave(string $filePath, string $logType = 'application'): array
    {
        $response = $this->fetchLogs($logType);

        if ($response->failed()) {
            return [
                'success' => false,
                'error' => 'Failed to fetch logs',
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }

        $content = $this->extractContent($response);

        if (empty($content)) {
            return [
                'success' => false,
                'error' => 'No log content available',
            ];
        }

        $saved = $this->saveToFile($content, $filePath);

        if (! $saved) {
            return [
                'success' => false,
                'error' => 'Failed to write logs to file',
            ];
        }

        return [
            'success' => true,
            'path' => $filePath,
            'size' => strlen($content),
        ];
    }

    /**
     * Fetch nginx access logs from Laravel Forge
     */
    public function fetchNginxAccessLogs(): Response
    {
        $url = $this->buildNginxAccessLogUrl();

        return Http::withToken($this->token ?? '')
            ->acceptJson()
            ->get($url);
    }

    /**
     * Fetch and save nginx access logs
     */
    public function fetchAndSaveNginxAccessLogs(string $filePath): array
    {
        $response = $this->fetchNginxAccessLogs();

        if ($response->failed()) {
            return [
                'success' => false,
                'error' => 'Failed to fetch nginx access logs',
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }

        $content = $this->extractContent($response);

        if (empty($content)) {
            return [
                'success' => false,
                'error' => 'No nginx access log content available',
            ];
        }

        $saved = $this->saveToFile($content, $filePath);

        if (! $saved) {
            return [
                'success' => false,
                'error' => 'Failed to write nginx access logs to file',
            ];
        }

        return [
            'success' => true,
            'path' => $filePath,
            'size' => strlen($content),
        ];
    }

    /**
     * Fetch nginx error logs from Laravel Forge
     */
    public function fetchNginxErrorLogs(): Response
    {
        $url = $this->buildNginxErrorLogUrl();

        return Http::withToken($this->token ?? '')
            ->acceptJson()
            ->get($url);
    }

    /**
     * Fetch and save nginx error logs
     */
    public function fetchAndSaveNginxErrorLogs(string $filePath): array
    {
        $response = $this->fetchNginxErrorLogs();

        if ($response->failed()) {
            return [
                'success' => false,
                'error' => 'Failed to fetch nginx error logs',
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }

        $content = $this->extractContent($response);

        if (empty($content)) {
            return [
                'success' => false,
                'error' => 'No nginx error log content available',
            ];
        }

        $saved = $this->saveToFile($content, $filePath);

        if (! $saved) {
            return [
                'success' => false,
                'error' => 'Failed to write nginx error logs to file',
            ];
        }

        return [
            'success' => true,
            'path' => $filePath,
            'size' => strlen($content),
        ];
    }

    /**
     * Build the Forge API URL for logs
     */
    private function buildLogUrl(string $logType): string
    {
        return sprintf(
            'https://forge.laravel.com/api/orgs/%s/servers/%d/sites/%d/logs/%s',
            $this->organization ?? '',
            $this->serverId ?? 0,
            $this->siteId ?? 0,
            $logType
        );
    }

    /**
     * Build the Forge API URL for nginx access logs
     */
    private function buildNginxAccessLogUrl(): string
    {
        return sprintf(
            'https://forge.laravel.com/api/orgs/%s/servers/%d/sites/%d/logs/nginx-access',
            $this->organization ?? '',
            $this->serverId ?? 0,
            $this->siteId ?? 0
        );
    }

    /**
     * Build the Forge API URL for nginx error logs
     */
    private function buildNginxErrorLogUrl(): string
    {
        return sprintf(
            'https://forge.laravel.com/api/orgs/%s/servers/%d/sites/%d/logs/nginx-error',
            $this->organization ?? '',
            $this->serverId ?? 0,
            $this->siteId ?? 0
        );
    }

    /**
     * Delete site logs via Forge API
     */
    public function deleteSiteLog(): array
    {
        if (empty($this->token) || empty($this->organization) || empty($this->serverId) || empty($this->siteId)) {
            return [
                'success' => false,
                'error' => 'Configuration incomplete. Run "php artisan forge-init" to configure.',
            ];
        }

        $apiService = new \FrankFlow\LaravelForgeLogs\Services\ForgeApiService($this->token);

        return $apiService->deleteSiteLog(
            $this->organization,
            $this->serverId,
            $this->siteId
        );
    }

    /**
     * Delete nginx access logs via Forge API
     */
    public function deleteNginxAccessLog(): array
    {
        if (empty($this->token) || empty($this->organization) || empty($this->serverId) || empty($this->siteId)) {
            return [
                'success' => false,
                'error' => 'Configuration incomplete. Run "php artisan forge-init" to configure.',
            ];
        }

        $apiService = new \FrankFlow\LaravelForgeLogs\Services\ForgeApiService($this->token);

        return $apiService->deleteNginxAccessLog(
            $this->organization,
            $this->serverId,
            $this->siteId
        );
    }

    /**
     * Delete nginx error logs via Forge API
     */
    public function deleteNginxErrorLog(): array
    {
        if (empty($this->token) || empty($this->organization) || empty($this->serverId) || empty($this->siteId)) {
            return [
                'success' => false,
                'error' => 'Configuration incomplete. Run "php artisan forge-init" to configure.',
            ];
        }

        $apiService = new \FrankFlow\LaravelForgeLogs\Services\ForgeApiService($this->token);

        return $apiService->deleteNginxErrorLog(
            $this->organization,
            $this->serverId,
            $this->siteId
        );
    }

    /**
     * Delete all logs via Forge API
     */
    public function deleteAllLogs(): array
    {
        if (empty($this->token) || empty($this->organization) || empty($this->serverId) || empty($this->siteId)) {
            return [
                'success' => false,
                'error' => 'Configuration incomplete. Run "php artisan forge-init" to configure.',
            ];
        }

        $apiService = new \FrankFlow\LaravelForgeLogs\Services\ForgeApiService($this->token);

        // Delete all three types of logs
        $results = [];

        $results['site'] = $apiService->deleteSiteLog(
            $this->organization,
            $this->serverId,
            $this->siteId
        );

        $results['nginx_access'] = $apiService->deleteNginxAccessLog(
            $this->organization,
            $this->serverId,
            $this->siteId
        );

        $results['nginx_error'] = $apiService->deleteNginxErrorLog(
            $this->organization,
            $this->serverId,
            $this->siteId
        );

        // Check if all were successful
        $allSuccessful = array_reduce($results, fn ($carry, $result) => $carry && $result['success'], true);

        if (! $allSuccessful) {
            return [
                'success' => false,
                'error' => 'Some logs failed to delete',
                'results' => $results,
            ];
        }

        return [
            'success' => true,
            'results' => $results,
        ];
    }

    /**
     * Validate configuration
     */
    public function validateConfig(): array
    {
        $errors = [];

        if (empty($this->token)) {
            $errors[] = 'FORGE_TOKEN not configured. Add your Forge API token to .env file.';
        }

        if (empty($this->organization)) {
            $errors[] = 'FORGE_ORGANIZATION not configured. Run "php artisan forge-init" to configure.';
        }

        if (empty($this->serverId)) {
            $errors[] = 'FORGE_SERVER_ID not configured. Run "php artisan forge-init" to configure.';
        }

        if (empty($this->siteId)) {
            $errors[] = 'FORGE_SITE_ID not configured. Run "php artisan forge-init" to configure.';
        }

        return $errors;
    }
}
