<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 150);
            $table->string('company_name', 150)->nullable();
            $table->string('email', 254)->index();
            $table->string('budget', 100)->nullable();
            $table->string('voucher_code', 50)->nullable()->index();
            $table->text('project_description')->nullable();
            $table->enum('status', ['new', 'in_review', 'accepted', 'rejected'])->default('new')->index();
            $table->timestamps();

            $table->foreign('voucher_code')
                ->references('code')
                ->on('vouchers')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('project_inquiries', function (Blueprint $table) {
            $table->dropForeign(['voucher_code']);
        });

        Schema::dropIfExists('project_inquiries');
    }
};
