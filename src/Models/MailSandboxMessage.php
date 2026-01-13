<?php

namespace AshishDhakal\MailSandbox\Models;

use Illuminate\Database\Eloquent\Model;

class MailSandboxMessage extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mail_sandbox_emails';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject',
        'to',
        'from',
        'cc',
        'bcc',
        'body_html',
        'body_text',
        'attachments',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'attachments' => 'json',
    ];
}
