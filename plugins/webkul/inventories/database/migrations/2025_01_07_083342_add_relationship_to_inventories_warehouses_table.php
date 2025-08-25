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
        Schema::table('inventories_warehouses', function (Blueprint $table) {
            $table->foreignId('view_location_id')
                ->constrained('inventories_locations')
                ->restrictOnDelete();

            $table->foreignId('lot_stock_location_id')
                ->constrained('inventories_locations')
                ->restrictOnDelete();

            $table->foreignId('input_stock_location_id')
                ->nullable()
                ->constrained('inventories_locations')
                ->nullOnDelete();

            $table->foreignId('qc_stock_location_id')
                ->nullable()
                ->constrained('inventories_locations')
                ->nullOnDelete();

            $table->foreignId('output_stock_location_id')
                ->nullable()
                ->constrained('inventories_locations')
                ->nullOnDelete();

            $table->foreignId('pack_stock_location_id')
                ->nullable()
                ->constrained('inventories_locations')
                ->nullOnDelete();

            $table->foreignId('mto_pull_id')
                ->nullable()
                ->constrained('inventories_rules')
                ->nullOnDelete();

            $table->foreignId('buy_pull_id')
                ->nullable()
                ->constrained('inventories_rules')
                ->nullOnDelete();

            $table->foreignId('pick_type_id')
                ->nullable()
                ->constrained('inventories_operation_types')
                ->nullOnDelete();

            $table->foreignId('pack_type_id')
                ->nullable()
                ->constrained('inventories_operation_types')
                ->nullOnDelete();

            $table->foreignId('out_type_id')
                ->nullable()
                ->constrained('inventories_operation_types')
                ->nullOnDelete();

            $table->foreignId('in_type_id')
                ->nullable()
                ->constrained('inventories_operation_types')
                ->nullOnDelete();

            $table->foreignId('internal_type_id')
                ->nullable()
                ->constrained('inventories_operation_types')
                ->nullOnDelete();

            $table->foreignId('qc_type_id')
                ->nullable()
                ->constrained('inventories_operation_types')
                ->nullOnDelete();

            $table->foreignId('store_type_id')
                ->nullable()
                ->constrained('inventories_operation_types')
                ->nullOnDelete();

            $table->foreignId('xdock_type_id')
                ->nullable()
                ->constrained('inventories_operation_types')
                ->nullOnDelete();

            $table->foreignId('crossdock_route_id')
                ->nullable()
                ->constrained('inventories_routes')
                ->restrictOnDelete();

            $table->foreignId('reception_route_id')
                ->nullable()
                ->constrained('inventories_routes')
                ->restrictOnDelete();

            $table->foreignId('delivery_route_id')
                ->nullable()
                ->constrained('inventories_routes')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories_warehouses', function (Blueprint $table) {
            $columnsToDrop = [
                'view_location_id',
                'lot_stock_location_id',
                'input_stock_location_id',
                'qc_stock_location_id',
                'output_stock_location_id',
                'pack_stock_location_id',
                'mto_pull_id',
                'buy_pull_id',
                'pick_type_id',
                'pack_type_id',
                'out_type_id',
                'in_type_id',
                'internal_type_id',
                'qc_type_id',
                'store_type_id',
                'xdock_type_id',
                'crossdock_route_id',
                'reception_route_id',
                'delivery_route_id',
            ];

            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('inventories_warehouses', $column)) {
                    $table->dropForeign([$column]);

                    $table->dropColumn($column);
                }
            }
        });
    }
};
