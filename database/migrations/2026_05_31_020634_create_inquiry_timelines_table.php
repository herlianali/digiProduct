<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiry_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')
                ->constrained('project_inquiries')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->enum('timeline', ['asap', '1_2_weeks', 'long_term', 'flexible']);

            $table->unique(['inquiry_id', 'timeline']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiry_timelines');
    }
};
