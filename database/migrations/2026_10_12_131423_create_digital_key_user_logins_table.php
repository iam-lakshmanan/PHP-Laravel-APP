<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_key_user_logins', function (Blueprint $table) {
            $table->increments('id');

            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('email', 191);

            // Reference to digital_key_keys table
            $table->unsignedInteger('key_id');

            // First-time login tracking
            $table->boolean('first_login')->default(false);
            $table->timestamp('first_login_at')->nullable();

            // active, inactive, blocked
            $table->string('login_status', 16)->default('active');

            $table->timestamps();

            $table->index('key_id');
            $table->index('email');
            $table->index('login_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_key_user_logins');
    }
};
