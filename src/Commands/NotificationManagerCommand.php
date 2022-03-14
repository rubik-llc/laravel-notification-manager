<?php

namespace Rubik\NotificationManager\Commands;

use Illuminate\Foundation\Console\NotificationMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class NotificationManagerCommand extends NotificationMakeCommand
{
    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
            ['subscribable', 's', InputOption::VALUE_NONE, 'Create subscribable notification classes'],
        ]);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->option('subscribable') ? __DIR__ . '/Stubs/make-subscribable-notification.stub' : parent::getStub();
    }
}
