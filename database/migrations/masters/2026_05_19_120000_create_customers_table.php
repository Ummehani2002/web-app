<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('company_id', 100);
            $table->string('type', 50);
            $table->string('customer_id', 100);
            $table->string('name', 255);
            $table->string('address_flat', 100)->nullable();
            $table->string('address_building', 100)->nullable();
            $table->string('address_area', 255)->nullable();
            $table->string('address_city', 100)->nullable();
            $table->string('address_emirates', 100)->nullable();
            $table->string('address_pincode', 20)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['company_id', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
