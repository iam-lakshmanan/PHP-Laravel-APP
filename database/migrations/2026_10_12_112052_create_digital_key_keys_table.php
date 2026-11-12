
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
        Schema::create('digital_key_keys', function (Blueprint $table) {

            // Primary key.
            $table->increments('key_id');

            // Client ID.
            $table->unsignedInteger('client_id');

            // Hashed digital key (unique).
            $table->string('key_hash', 128)->unique();

            // Optional key label.
            $table->string('label', 255)->nullable();

            // Key status.
            $table->string('status', 16)->default('active');

            // Expiry timestamp.
            $table->integer('expires')->nullable();

            // Maximum permitted uses.
            $table->unsignedInteger('max_uses')->nullable();

            // Number of times the key has been used.
            $table->unsignedInteger('use_count')->default(0);

            // Drupal-compatible Unix timestamp.
            $table->integer('created')->default(0);

            // Drupal user ID.
            $table->unsignedInteger('uid')->default(0);

            // Indexes.
            $table->index('client_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_key_keys');
    }
};
