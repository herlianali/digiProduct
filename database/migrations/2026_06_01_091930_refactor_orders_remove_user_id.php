<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // 1. Drop foreign key dulu sebelum drop column
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            // 2. Tambah kolom baru
            // $table->string('order_number')->unique()->change(); // sudah ada, skip jika error
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Rollback: kembalikan user_id (nullable agar bisa rollback tanpa data)
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }
};
