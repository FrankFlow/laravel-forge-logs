<?php

namespace FrankFlow\LaravelForgeLogs\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ForgeApiService
{
    private string $token;

    private string $baseUrl = 'https://forge.laravel.com/api';

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Create service instance from config
     */
    public static function fromConfig(): self
    {
        return new self(config('forge-logs.forge_token'));
    }

    /**
     * List all organizations
     *
     * @return array{success: bool, data?: array, error?: string, status?: int, body?: string}
     */
    public function listOrganizations(): array
    {
        $response = $this->makeRequest('GET', '/orgs');

        if ($response->failed()) {
            return [
                'success' => false,
                'error' => 'Failed to fetch organizations',
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }

        $data = $response->json();

        // Transform JSON:API format to simple array
        $organizations = collect($data['data'] ?? [])
            ->map(fn ($org) => [
                'id' => $org['id'],
                'name' => $org['attributes']['name'],
                'slug' => $org['attributes']['slug'],
            ])
            ->toArray();

        return [
            'success' => true,
            'data' => $organizations,
        ];
    }

    /**
     * List servers for an organization
     *
     * @return array{success: bool, data?: array, error?: string, status?: int, body?: string}
     */
    public function listServersForOrganization(string $orgSlug): array
    {
        $response = $this->makeRequest('GET', "/orgs/{$orgSlug}/servers");

        if ($response->failed()) {
            return [
                'success' => false,
                'error' => 'Failed to fetch servers',
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }

        $data = $response->json();

        // Transform JSON:API format to simple array
        $servers = collect($data['data'] ?? [])
            ->map(fn ($server) => [
                'id' => $server['id'],
                'name' => $server['attributes']['name'] ?? 'Server #'.$server['id'],
                'ip_address' => $server['attributes']['ip_address'] ?? null,
            ])
            ->toArray();

        return [
            'success' => true,
            'data' => $servers,
        ];
    }

    /**
     * List sites for a specific server
     *
     * @return array{success: bool, data?: array, error?: string, status?: int, body?: string}
     */
    public function listSitesForServer(string $orgSlug, int $serverId): array
    {
        $response = $this->makeRequest('GET', "/orgs/{$orgSlug}/servers/{$serverId}/sites");

        if ($response->failed()) {
            return [
                'success' => false,
                'error' => 'Failed to fetch sites',
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }

        $data = $response->json();

        // Transform JSON:API format to simple array
        $sites = collect($data['data'] ?? [])
            ->map(fn ($site) => [
                'id' => $site['id'],
                'name' => $site['attributes']['name'],
                'url' => $site['attributes']['url'] ?? null,
            ])
            ->toArray();

        return [
            'success' => true,
            'data' => $sites,
        ];
    }

    /**
     * Get site details
     *
     * @return array{success: bool, data?: array, error?: string, status?: int, body?: string}
     */
    public function getSite(string $orgSlug, int $serverId, int $siteId): array
    {
        $response = $this->makeRequest('GET', "/orgs/{$orgSlug}/servers/{$serverId}/sites/{$siteId}");

        if ($response->failed()) {
            return [
                'success' => false,
                'error' => 'Failed to fetch site details',
                'status' => $response->status(),
            ];
        }

        $data = $response->json();

        return [
            'success' => true,
            'data' => $data['site'] ?? [],
        ];
    }

    /**
     * Make HTTP request to Forge API
     */
    private function makeRequest(string $method, string $endpoint): Response
    {
        return Http::withToken($this->token)
            ->acceptJson()
            ->send($method, $this->baseUrl.$endpoint);
    }

    /**
     * Validate if token is set
     */
    public function hasToken(): bool
    {
        return ! empty($this->token);
    }
}
