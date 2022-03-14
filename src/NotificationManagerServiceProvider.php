<?php

namespace Rubik\NotificationManager;

use Illuminate\Notifications\Channels\DatabaseChannel as BaseDatabaseChannel;
use Illuminate\Notifications\DatabaseNotification as BaseDatabaseNotification;
use Rubik\NotificationManager\Channels\DatabaseChannel;
use Rubik\NotificationManager\Commands\NotificationManagerCommand;
use Rubik\NotificationManager\Models\DatabaseNotification;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NotificationManagerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('notification-manager')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_notification_manager_table')
            ->hasCommand(NotificationManagerCommand::class);
    }

    public function packageRegistered()
    {
    }

    public function packageBooted()
    {
        $this->app->instance(BaseDatabaseChannel::class, new DatabaseChannel());
        $this->app->instance(BaseDatabaseNotification::class, new DatabaseNotification());
    }
}
