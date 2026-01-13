<?php

use AshishDhakal\MailSandbox\Http\Controllers\MailSandboxController;
use Illuminate\Support\Facades\Route;

if (config('mail-sandbox.ui_enabled', true)) {
    Route::prefix(config('mail-sandbox.path', 'mail-sandbox'))
        ->middleware(config('mail-sandbox.middleware', ['web']))
        ->name('mail-sandbox.')
        ->group(function () {
            Route::get('/', [MailSandboxController::class, 'index'])->name('index');
            Route::get('/logo', [MailSandboxController::class, 'logo'])->name('logo');
            Route::get('/{email}', [MailSandboxController::class, 'show'])->name('show');
            Route::get('/{email}/content', [MailSandboxController::class, 'content'])->name('content');
            Route::get('/{email}/attachments/{index}', [MailSandboxController::class, 'download'])->name('download');
            Route::delete('/', [MailSandboxController::class, 'clear'])->name('clear');
        });
}
