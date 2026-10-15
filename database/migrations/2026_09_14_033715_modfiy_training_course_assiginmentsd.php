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
        Schema::table('training_course_assiginments', function (Blueprint $table) {
            // $table->dropForeign(['training_course_id']);
            // $table->dropColumn('training_course_id');
            // $table->foreignId('instructor_id')->constrained('instructors')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_course_assiginments', function (Blueprint $table) {
            //
        });
    }
};
