
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
        Schema::create('digital_key_downloads', function (Blueprint $table) {

            // Primary key.
            $table->increments('download_id');

            // Client ID.
            $table->unsignedInteger('client_id');

            // Document ID.
            $table->unsignedInteger('document_id');

            // Drupal user ID.
            $table->unsignedInteger('uid')->default(0);

            // Download result.
            $table->string('result', 16)->default('denied');

            // IPv4 or IPv6 address.
            $table->string('ip_address', 45)->default('');

            // Drupal-compatible Unix timestamp.
            $table->integer('created')->default(0);

            // Indexes.
            $table->index('client_id');
            $table->index('document_id');
            $table->index('uid');
            $table->index('created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_key_downloads');
    }
};
