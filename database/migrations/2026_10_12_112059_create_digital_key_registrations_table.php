
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
        Schema::create('digital_key_registrations', function (Blueprint $table) {

            // Primary key.
            $table->increments('registration_id');

            // Client ID.
            $table->unsignedInteger('client_id');

            // Drupal user ID.
            $table->unsignedInteger('uid');

            // Digital key ID.
            $table->unsignedInteger('key_id');

            // Registration status: 1 = Active, 0 = Inactive.
            $table->tinyInteger('status')->default(1);

            // Drupal-compatible Unix timestamp.
            $table->integer('created')->default(0);

            // Unique combination of user and client.
            $table->unique(['uid', 'client_id'], 'uid_client');

            // Indexes.
            $table->index('client_id');
            $table->index('key_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_key_registrations');
    }
};
