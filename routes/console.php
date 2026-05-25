<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mail:test {email}', function (string $email) {
    Mail::raw(
        "This is a test email from CodeXpress Institute.\n\nIf you received this, the portal email delivery is configured correctly.",
        fn ($message) => $message->to($email)->subject('CodeXpress email test')
    );

    $this->info("Test email sent to {$email} using mailer: ".config('mail.default'));
})->purpose('Send a test email through the configured Laravel mailer');
