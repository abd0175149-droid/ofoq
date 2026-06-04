<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // تعديل جدول الفواتير (فقط إذا الجدول موجود والأعمدة مفقودة)
        if (Schema::hasTable('invoices') && !Schema::hasColumn('invoices', 'client_phone')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('client_phone', 20)->nullable()->after('client_id');
                $table->date('trip_date')->nullable()->after('client_phone');
                $table->decimal('total_sell_jod', 15, 3)->default(0)->after('total_jod');
                $table->decimal('total_cost_sar', 15, 2)->default(0)->after('total_sell_jod');
                $table->decimal('total_cost_jod', 15, 3)->default(0)->after('total_cost_sar');
                $table->decimal('discount_jod', 15, 3)->default(0)->after('total_cost_jod');

                $table->foreignId('agent_id')->nullable()->change();
            });
        }

        // تعديل جدول بنود الفاتورة
        if (Schema::hasTable('invoice_items') && !Schema::hasColumn('invoice_items', 'agent_id')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->foreignId('agent_id')->nullable()->after('invoice_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['client_phone', 'trip_date', 'total_sell_jod', 'total_cost_sar', 'total_cost_jod', 'discount_jod']);
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('agent_id');
        });
    }
};
