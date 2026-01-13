<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mail_sandbox_emails', function (Blueprint $table) {
            $table->id();
            $table->string('subject')->nullable();
            $table->text('to')->nullable();
            $table->text('from')->nullable();
            $table->text('cc')->nullable();
            $table->text('bcc')->nullable();
            $table->longText('body_html')->nullable();
            $table->longText('body_text')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_sandbox_emails');
    }
};
