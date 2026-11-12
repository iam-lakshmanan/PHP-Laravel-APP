<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_key_email_otps', function (Blueprint $table) {
            $table->id();

            // Match digital_key_user_logins.id: INT UNSIGNED
            $table->unsignedInteger('user_id');

            $table->string('email')->index();
            $table->string('otp_hash');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('digital_key_user_logins')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_key_email_otps');
    }
};
