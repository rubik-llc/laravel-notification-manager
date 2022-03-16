<?php

it('will pass', function () {
    $this->artisan('make:notification TestNotification -s')->assertNotExitCode(1);

    $this->artisan('make:notification TestNotification -sf')->assertNotExitCode(1);
    $this->artisan('make:notification TestNotification -fs')->assertNotExitCode(1);

    $this->artisan('make:notification TestNotification -sm')->assertNotExitCode(1);
    $this->artisan('make:notification TestNotification -ms')->assertNotExitCode(1);

    $this->artisan('make:notification TestNotification -sfm')->assertNotExitCode(1);
    $this->artisan('make:notification TestNotification -smf')->assertNotExitCode(1);

    $this->artisan('make:notification TestNotification -msf')->assertNotExitCode(1);
    $this->artisan('make:notification TestNotification -fsm')->assertNotExitCode(1);

    $this->artisan('make:notification TestNotification -fms')->assertNotExitCode(1);
    $this->artisan('make:notification TestNotification -mfs')->assertNotExitCode(1);
});
