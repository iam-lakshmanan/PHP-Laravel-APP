
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
        Schema::create('digital_key_clients', function (Blueprint $table) {

            // Primary key: Auto-incrementing client ID.
            $table->increments('client_id');

            // Client information.
            $table->string('name', 255)->default('');

            // Request date.
            $table->string('request_date', 10)->nullable();

            // Number of digital keys.
            $table->unsignedInteger('number_of_keys')->default(1);

            // Whether printing is allowed.
            $table->boolean('printable')->default(false);

            // Client status: 1 = Active, 0 = Inactive.
            $table->tinyInteger('status')->default(1);

            // Drupal-compatible Unix timestamps.
            $table->integer('created')->default(0);
            $table->integer('changed')->default(0);

            // Index for status.
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_key_clients');
    }
};
