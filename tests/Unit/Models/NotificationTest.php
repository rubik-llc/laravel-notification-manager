<?php


use Rubik\NotificationManager\Models\Notification;

it('marks a notification as seen', function () {
    $notification = Notification::factory()->create();
    $this->assertTrue($notification->seen_at === null);
    $notification->markAsSeen();
    $this->assertTrue($notification->seen_at !== null);
});


it('marks a notification as unseen', function () {
    $notification = Notification::factory()->create();
    $this->assertTrue($notification->seen_at === null);
    $notification->markAsSeen();
    $this->assertTrue($notification->seen_at !== null);
    $notification->markAsUnseen();
    $this->assertTrue($notification->seen_at === null);
});

it('checks if a notification is seen', function () {
    $notification = Notification::factory()->create();
    $notification->markAsSeen();
    $this->assertTrue($notification->seen() === true);
});

it('checks if a notification is unseen', function () {
    $notification = Notification::factory()->create();
    $this->assertTrue($notification->unseen() === true);
});

it('checks if a notification is prioritized', function () {
    $notification = Notification::factory()->state(['is_prioritized' => true])->create();
    $this->assertTrue($notification->prioritized() === true);
});

it('checks if a notification is trivialized', function () {
    $notification = Notification::factory()->state(['is_prioritized' => false])->create();
    $this->assertTrue($notification->trivialized() === true);
});

it('checks if a notification is muted', function () {
    $notification = Notification::factory()->state(['is_muted' => true])->create();
    $this->assertTrue($notification->muted() === true);
});

it('checks if a notification needs authentication', function () {
    $notification = Notification::factory()->state(['needs_authentication' => true])->create();
    $this->assertTrue($notification->needsAuthentication() === true);
});
