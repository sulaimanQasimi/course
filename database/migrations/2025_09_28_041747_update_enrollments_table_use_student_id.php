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
        Schema::table('enrollments', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['user_id']);
            
            // Drop the existing index
            $table->dropIndex(['user_id', 'status']);
            
            // Rename the column
            $table->renameColumn('user_id', 'student_id');
            
            // Add new foreign key constraint
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            
            // Add new index
            $table->index(['student_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['student_id']);
            
            // Drop the new index
            $table->dropIndex(['student_id', 'status']);
            
            // Rename the column back
            $table->renameColumn('student_id', 'user_id');
            
            // Add back the original foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Add back the original index
            $table->index(['user_id', 'status']);
        });
    }
};
