<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiry_scopes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')
                ->constrained('project_inquiries')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->enum('scope', ['design_service', 'illustration_service']);

            $table->unique(['inquiry_id', 'scope']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiry_scopes');
    }
};
