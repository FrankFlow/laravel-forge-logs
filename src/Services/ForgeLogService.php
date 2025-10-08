<?php

namespace FrankFlow\LaravelForgeLogs\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ForgeLogService
{
    private string $token;

    private string $organization;

    private int $serverId;

    private int $siteId;

    public function __construct(
        string $token,
        string $organization,
        int $serverId,
        int $siteId
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
            config('forge-logs.forge_org'),
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

        return Http::withToken($this->token)
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
     * Build the Forge API URL for logs
     */
    private function buildLogUrl(string $logType): string
    {
        return sprintf(
            'https://forge.laravel.com/api/orgs/%s/servers/%d/sites/%d/logs/%s',
            $this->organization,
            $this->serverId,
            $this->siteId,
            $logType
        );
    }

    /**
     * Validate configuration
     */
    public function validateConfig(): array
    {
        $errors = [];

        if (empty($this->token)) {
            $errors[] = 'FORGE_TOKEN not configured';
        }

        if (empty($this->serverId)) {
            $errors[] = 'FORGE_SERVER_ID not configured';
        }

        if (empty($this->siteId)) {
            $errors[] = 'FORGE_SITE_ID not configured';
        }

        if (empty($this->organization)) {
            $errors[] = 'FORGE_ORGANIZATION not configured';
        }

        return $errors;
    }
}
