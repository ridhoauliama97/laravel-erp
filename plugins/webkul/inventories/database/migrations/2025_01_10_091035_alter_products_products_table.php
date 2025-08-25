<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Webkul\Inventory\Enums\ProductTracking;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products_products', function (Blueprint $table) {
            $table->integer('sale_delay')->nullable();
            $table->string('tracking')->nullable()->default(ProductTracking::QTY);
            $table->text('description_picking')->nullable();
            $table->text('description_pickingout')->nullable();
            $table->text('description_pickingin')->nullable();
            $table->boolean('is_storable')->nullable()->default(0);
            $table->integer('expiration_time')->nullable()->default(0);
            $table->integer('use_time')->nullable()->default(0);
            $table->integer('removal_time')->nullable()->default(0);
            $table->integer('alert_time')->nullable()->default(0);
            $table->boolean('use_expiration_date')->nullable()->default(0);

            $table->foreignId('responsible_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_products', function (Blueprint $table) {
            if (Schema::hasColumn('products_products', 'responsible_id')) {
                $table->dropForeign(['responsible_id']);

                $table->dropColumn('responsible_id');
            }

            $columnsToDrop = [
                'sale_delay',
                'tracking',
                'description_picking',
                'description_pickingout',
                'description_pickingin',
                'is_storable',
                'expiration_time',
                'use_time',
                'removal_time',
                'alert_time',
                'use_expiration_date',
            ];

            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('products_products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
