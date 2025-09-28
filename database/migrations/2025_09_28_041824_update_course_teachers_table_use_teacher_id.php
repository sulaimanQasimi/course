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
        Schema::table('course_teachers', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['user_id']);
            
            // Drop the existing index
            $table->dropIndex(['user_id', 'is_active']);
            
            // Rename the column
            $table->renameColumn('user_id', 'teacher_id');
            
            // Add new foreign key constraint
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            
            // Add new index
            $table->index(['teacher_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_teachers', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['teacher_id']);
            
            // Drop the new index
            $table->dropIndex(['teacher_id', 'is_active']);
            
            // Rename the column back
            $table->renameColumn('teacher_id', 'user_id');
            
            // Add back the original foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Add back the original index
            $table->index(['user_id', 'is_active']);
        });
    }
};
