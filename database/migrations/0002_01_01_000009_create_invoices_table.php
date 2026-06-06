<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * إنشاء جدولي الفواتير والبنود — كان مفقوداً من الـ migrations الأصلية
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number', 30)->unique();
                $table->foreignId('client_id')->nullable()->constrained('clients');
                $table->string('client_phone', 20)->nullable();
                $table->date('trip_date')->nullable();
                $table->foreignId('agent_id')->nullable()->constrained('agents');
                $table->decimal('exchange_rate_snapshot', 10, 6)->default(0.190000);
                $table->decimal('subtotal_sar', 15, 2)->default(0);
                $table->decimal('discount_sar', 15, 2)->default(0);
                $table->decimal('total_sar', 15, 2)->default(0);
                $table->decimal('total_jod', 15, 3)->default(0);
                $table->decimal('total_sell_jod', 15, 3)->default(0);
                $table->decimal('total_cost_sar', 15, 2)->default(0);
                $table->decimal('total_cost_jod', 15, 3)->default(0);
                $table->decimal('discount_jod', 15, 3)->default(0);
                $table->decimal('services_cost_sar', 15, 2)->default(0);
                $table->decimal('profit_sar', 15, 2)->default(0);
                $table->decimal('profit_jod', 15, 3)->default(0);
                $table->date('invoice_date')->nullable();
                $table->date('due_date')->nullable();
                $table->text('notes')->nullable();
                $table->string('status', 20)->default('draft');
                $table->text('rejection_reason')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->foreignId('approved_by')->nullable()->constrained('users');
                $table->timestamp('approved_at')->nullable();
                $table->unsignedBigInteger('modified_by')->nullable();
                $table->datetime('modified_at')->nullable();
                $table->text('original_values')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index('status');
                $table->index('invoice_date');
                $table->index('client_id');
            });
        }

        if (!Schema::hasTable('invoice_items')) {
            Schema::create('invoice_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
                $table->foreignId('agent_id')->nullable()->constrained('agents');
                $table->string('item_type', 30)->default('service'); // visa, service, other
                $table->foreignId('service_id')->nullable()->constrained('services');
                $table->string('description', 255)->nullable();
                $table->text('statement')->nullable();
                $table->integer('quantity')->default(1);
                $table->decimal('unit_price_sar', 15, 2)->default(0);
                $table->decimal('unit_price_jod', 15, 3)->default(0);
                $table->decimal('total_cost_sar', 15, 2)->default(0);
                $table->decimal('total_cost_jod', 15, 3)->default(0);
                $table->decimal('sell_price_jod', 15, 3)->default(0);
                $table->decimal('total_sell_jod', 15, 3)->default(0);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
