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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->enum('payment_method', ['transfer', 'cod']);
            $table->enum('status', [
                'pending',
                'menunggu_validasi',
                'diproses',
                'dikirim',
                'selesai',
                'dibatalkan',
            ])->default('pending');
            $table->decimal('total', 15, 2);
            $table->string('shipping_address');
            $table->string('shipping_phone');
            $table->string('payment_proof_path')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
