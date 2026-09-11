<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('gender')->nullable()->after('address');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->string('course')->nullable()->after('date_of_birth');
            $table->string('class_name')->nullable()->after('course');
            $table->string('department')->nullable()->after('class_name');
            $table->string('status')->default('Active')->after('department');
            $table->string('profile_photo')->nullable()->after('status');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->dropColumn([
                'phone',
                'address',
                'gender',
                'date_of_birth',
                'course',
                'class_name',
                'department',
                'status',
                'profile_photo',
            ]);
        });
    }
};