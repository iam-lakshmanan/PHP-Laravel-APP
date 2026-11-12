<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('digital_key_documents', function (Blueprint $table) {
            $table->boolean('editable_files')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('digital_key_documents', function (Blueprint $table) {
            $table->dropColumn('editable_files');
        });
    }
};
