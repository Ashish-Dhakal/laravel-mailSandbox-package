<?php

use Illuminate\Support\Facades\DB;

it('package is loaded', function () {
    expect(config('mail-sandbox'))->not->toBeNull();
});

it('can interact with the database', function () {
    DB::table('mail_sandbox_emails')->insert([
        'to' => 'test@example.com',
        'from' => 'admin@example.com',
        'subject' => 'Test Email',
        'body_html' => '<h1>Hello World</h1>',
        'body_text' => 'Hello World',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect(DB::table('mail_sandbox_emails')->count())->toBe(1);
});
