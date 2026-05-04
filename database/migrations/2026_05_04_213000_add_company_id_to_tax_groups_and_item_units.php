<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_tax_groups', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
            $table->dropUnique('sales_tax_groups_tax_group_id_unique');
            $table->unique(['company_id', 'tax_group_id']);
        });

        Schema::table('item_sales_tax_groups', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
            $table->dropUnique('item_sales_tax_groups_tax_item_group_unique');
            $table->unique(['company_id', 'tax_item_group']);
        });

        Schema::table('item_units', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
            $table->dropUnique('item_units_item_id_unit_id_unique');
            $table->unique(['company_id', 'item_id', 'unit_id']);
        });
    }

    public function down(): void
    {
        Schema::table('item_units', function (Blueprint $table) {
            $table->dropUnique('item_units_company_id_item_id_unit_id_unique');
            $table->unique(['item_id', 'unit_id']);
            $table->dropConstrainedForeignId('company_id');
        });

        Schema::table('item_sales_tax_groups', function (Blueprint $table) {
            $table->dropUnique('item_sales_tax_groups_company_id_tax_item_group_unique');
            $table->unique('tax_item_group');
            $table->dropConstrainedForeignId('company_id');
        });

        Schema::table('sales_tax_groups', function (Blueprint $table) {
            $table->dropUnique('sales_tax_groups_company_id_tax_group_id_unique');
            $table->unique('tax_group_id');
            $table->dropConstrainedForeignId('company_id');
        });
    }
};
