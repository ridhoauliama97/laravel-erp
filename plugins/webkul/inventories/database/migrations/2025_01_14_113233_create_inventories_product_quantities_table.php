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
        Schema::create('inventories_product_quantities', function (Blueprint $table) {
            $table->id();
            $table->decimal('quantity', 15, 4)->nullable()->default(0);
            $table->decimal('reserved_quantity', 15, 4)->default(0);
            $table->decimal('counted_quantity', 15, 4)->nullable()->default(0);
            $table->decimal('difference_quantity', 15, 4)->nullable()->default(0);
            $table->decimal('inventory_diff_quantity', 15, 4)->nullable()->default(0);
            $table->boolean('inventory_quantity_set')->default(0);
            $table->date('scheduled_at')->nullable();
            $table->datetime('incoming_at');

            $table->foreignId('product_id')
                ->constrained('products_products')
                ->restrictOnDelete();

            $table->foreignId('location_id')
                ->constrained('inventories_locations')
                ->restrictOnDelete();

            $table->foreignId('storage_category_id')
                ->nullable()
                ->constrained('inventories_storage_categories')
                ->nullOnDelete();

            $table->foreignId('lot_id')
                ->nullable()
                ->constrained('inventories_lots')
                ->restrictOnDelete();

            $table->foreignId('package_id')
                ->nullable()
                ->constrained('inventories_packages')
                ->restrictOnDelete();

            $table->foreignId('partner_id')
                ->nullable()
                ->constrained('partners_partners')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories_product_quantities');
    }
};
