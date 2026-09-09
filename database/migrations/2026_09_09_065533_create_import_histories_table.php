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
        Schema::create('import_histories', function (Blueprint $table) {
            $table->id();

            $table->string('file_name');

            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('imported_rows')->default(0);
            $table->unsignedInteger('duplicate_rows')->default(0);
            $table->unsignedInteger('failed_rows')->default(0);

            $table->string('status')->default('completed');

            // Stores row-level import errors
            $table->json('error_details')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_histories');
    }
};