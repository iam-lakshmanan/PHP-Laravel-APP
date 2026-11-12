
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
        Schema::create('digital_key_documents', function (Blueprint $table) {

            // Primary key.
            $table->increments('document_id');

            // Client ID (optional).
            $table->unsignedInteger('client_id')->nullable()->default(0);

            // Drupal file ID.
            $table->unsignedInteger('fid');

            // Document title.
            $table->string('title', 255)->default('');

            // Document status: 1 = Active, 0 = Inactive.
            $table->tinyInteger('status')->default(1);

            // Drupal-compatible Unix timestamp.
            $table->integer('created')->default(0);

            // Drupal user ID.
            $table->unsignedInteger('uid')->default(0);

            // Box integration fields.
            $table->string('box_file_id', 128)->default('');
            $table->string('box_file_url', 512)->default('');
            $table->string('box_status', 32)->default('');
            $table->integer('box_uploaded')->default(0);

            // Indexes.
            $table->index('client_id');
            $table->index('fid');
            $table->index('status');
            $table->index('box_file_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_key_documents');
    }
};
