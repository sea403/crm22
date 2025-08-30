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
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->default(0);
            $table->integer('template_id')->default(0);
            $table->string('subject');
            $table->string('from');
            $table->string('to')->comment('the receiver mail');
            $table->text('body')->comment('the email body');
            $table->boolean('sent_from_system')->default(true)->comment('sent from this sytem or comes from the IMAP');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
