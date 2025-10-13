<?php

namespace FrankFlow\LaravelForgeLogs;

use FrankFlow\LaravelForgeLogs\Commands\LaravelForgeAllLogsCommand;
use FrankFlow\LaravelForgeLogs\Commands\LaravelForgeFetchAllLogsCommand;
use FrankFlow\LaravelForgeLogs\Commands\LaravelForgeFetchLogCommand;
use FrankFlow\LaravelForgeLogs\Commands\LaravelForgeFetchNginxAccessLogCommand;
use FrankFlow\LaravelForgeLogs\Commands\LaravelForgeInitCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
                LaravelForgeInitCommand::class,
                LaravelForgeFetchLogCommand::class,
                LaravelForgeFetchNginxAccessLogCommand::class,
                LaravelForgeFetchAllLogsCommand::class,
                LaravelForgeAllLogsCommand::class,
            ]);
    }
}
