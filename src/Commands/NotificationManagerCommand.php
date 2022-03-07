<?php

namespace Rubik\NotificationManager\Commands;

use Illuminate\Console\Command;

class NotificationManagerCommand extends Command
{
    public $signature = 'laravel-notification-manager';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
