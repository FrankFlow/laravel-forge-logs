<?php

namespace FrankFlow\LaravelForgeLogs;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use FrankFlow\LaravelForgeLogs\Commands\LaravelForgeLogsCommand;
use FrankFlow\LaravelForgeLogs\Commands\LaravelForgeFetchLogCommand;

class LaravelForgeLogsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-forge-logs')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_forge_logs_table')
            ->hasCommands([
                LaravelForgeLogsCommand::class,
                LaravelForgeFetchLogCommand::class,
            ]);
    }
}
