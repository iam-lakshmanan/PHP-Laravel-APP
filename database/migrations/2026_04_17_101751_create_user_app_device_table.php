<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_app_device', function (Blueprint $table) {
            $table->id();

            $table->string('device_id')->nullable();
            $table->string('fcm_token')->nullable()->index();

            $table->string('platform')->default('android');

            $table->timestamp('last_active_at')->nullable();

            $table->timestamps(); // ✅ THIS LINE ADDS created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_app_device');
    }
};
