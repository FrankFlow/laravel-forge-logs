<?php

namespace FrankFlow\LaravelForgeLogs\Commands;

use FrankFlow\LaravelForgeLogs\Services\ForgeApiService;
use FrankFlow\LaravelForgeLogs\Services\WriteEnvFile;
use Illuminate\Console\Command;
use function Laravel\Prompts\text;
use function Laravel\Prompts\select;
use function Laravel\Prompts\search;
use function Laravel\Prompts\info;
use function Laravel\Prompts\error;
use function Laravel\Prompts\spin;

class LaravelForgeInitCommand extends Command
{
    public $signature = 'forge-init';

    public $description = 'Initialize Laravel Forge configuration by selecting organization and site';

    public function handle(): int
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            error('.env file not found!');
            return self::FAILURE;
        }

        // Get token from environment
        $token = config('forge-logs.forge_token');

        if (empty($token)) {
            error('FORGE_TOKEN not found in .env file. Please add it manually.');
            return self::FAILURE;
        }

        $apiService = new ForgeApiService($token);

        // Fetch organizations
        $orgsResult = spin(
            fn () => $apiService->listOrganizations(),
            'Fetching organizations...'
        );

        if (!$orgsResult['success']) {
            error($orgsResult['error'] ?? 'Failed to fetch organizations');
            if (isset($orgsResult['status'])) {
                $this->error("HTTP Status: {$orgsResult['status']}");
            }
            if (isset($orgsResult['body'])) {
                $this->error("Response: {$orgsResult['body']}");
            }
            return self::FAILURE;
        }

        if (empty($orgsResult['data'])) {
            error('No organizations found');
            return self::FAILURE;
        }

        // Prepare organizations for selection
        $orgChoices = collect($orgsResult['data'])
            ->mapWithKeys(fn ($org) => [$org['slug'] => $org['name']])
            ->toArray();

        $selectedOrgSlug = select(
            label: 'Select your organization',
            options: $orgChoices,
            required: true
        );

        // Fetch servers for selected organization
        $serversResult = spin(
            fn () => $apiService->listServersForOrganization($selectedOrgSlug),
            'Fetching servers...'
        );

        if (!$serversResult['success']) {
            error($serversResult['error'] ?? 'Failed to fetch servers');
            if (isset($serversResult['status'])) {
                $this->error("HTTP Status: {$serversResult['status']}");
            }
            if (isset($serversResult['body'])) {
                $this->error("Response: {$serversResult['body']}");
            }
            return self::FAILURE;
        }

        if (empty($serversResult['data'])) {
            error('No servers found for this organization');
            return self::FAILURE;
        }

        // Prepare servers for selection
        $serverChoices = collect($serversResult['data'])
            ->mapWithKeys(fn ($server) => [
                $server['id'] => "{$server['name']} ({$server['ip_address']})"
            ])
            ->toArray();

        $serverId = select(
            label: 'Select your server',
            options: $serverChoices,
            required: true
        );

        // Fetch sites for selected server
        $sitesResult = spin(
            fn () => $apiService->listSitesForServer($selectedOrgSlug, (int) $serverId),
            'Fetching sites...'
        );

        if (!$sitesResult['success']) {
            error($sitesResult['error'] ?? 'Failed to fetch sites');
            if (isset($sitesResult['status'])) {
                $this->error("HTTP Status: {$sitesResult['status']}");
            }
            if (isset($sitesResult['body'])) {
                $this->error("Response: {$sitesResult['body']}");
            }
            return self::FAILURE;
        }

        if (empty($sitesResult['data'])) {
            error('No sites found for this server');
            return self::FAILURE;
        }

        // Prepare sites for search
        $sites = $sitesResult['data'];

        $siteId = search(
            label: 'Search and select your site',
            placeholder: 'Type to search...',
            options: fn (string $value) => strlen($value) > 0
                ? collect($sites)
                    ->filter(fn ($site) =>
                        stripos($site['name'], $value) !== false ||
                        stripos($site['url'] ?? '', $value) !== false
                    )
                    ->mapWithKeys(fn ($site) => [
                        $site['id'] => "{$site['name']} ({$site['url']})"
                    ])
                    ->toArray()
                : collect($sites)
                    ->mapWithKeys(fn ($site) => [
                        $site['id'] => "{$site['name']} ({$site['url']})"
                    ])
                    ->toArray()
        );

        // Write to .env file
        WriteEnvFile::writeEnv($selectedOrgSlug, (int) $serverId, (int) $siteId, $envPath);

        info('✓ Configuration saved to .env file!');
        $this->components->info("Organization: {$selectedOrgSlug}");
        $this->components->info("Server ID: {$serverId}");
        $this->components->info("Site ID: {$siteId}");

        return self::SUCCESS;
    }
}
