<?php

namespace FrankFlow\LaravelForgeLogs\Services;

class WriteEnvFile
{

    public static function writeEnv(string $organization, int $serverId, int $siteId, string $envPath)
    {


        // Leggi il contenuto attuale
        $envContent = file_get_contents($envPath);

        // Aggiungi o aggiorna FORGE_SERVER_ID
        if (preg_match('/^FORGE_SERVER_ID=/m', $envContent)) {
            $envContent = preg_replace(
                '/^FORGE_SERVER_ID=.*/m',
                "FORGE_SERVER_ID={$serverId}",
                $envContent
            );
        } else {
            $envContent .= "\nFORGE_SERVER_ID={$serverId}";
        }

        // Aggiungi o aggiorna FORGE_SITE_ID
        if (preg_match('/^FORGE_SITE_ID=/m', $envContent)) {
            $envContent = preg_replace(
                '/^FORGE_SITE_ID=.*/m',
                "FORGE_SITE_ID={$siteId}",
                $envContent
            );
        } else {
            $envContent .= "\nFORGE_SITE_ID={$siteId}";
        }

        // Aggiungi o aggiorna FORGE_SITE_ID
        if (preg_match('/^FORGE_ORGANIZATION=/m', $envContent)) {
            $envContent = preg_replace(
                '/^FORGE_ORGANIZATION=.*/m',
                "FORGE_ORGANIZATION={$organization}",
                $envContent
            );
        } else {
            $envContent .= "\nFORGE_ORGANIZATION={$organization}";
        }

        // Scrivi il file
        file_put_contents($envPath, $envContent);
    }
}
