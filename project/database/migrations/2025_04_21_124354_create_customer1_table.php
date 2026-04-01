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
        Schema::create('customer1', function (Blueprint $table) {
            $table->id('cust_id');
            $table->string ('name',60);
            $table->string ('email',100);
             $table->enum('gender',["M","F","O"]);
             $table->TEXT('ADDRESS');
             $table->date('dob');
             $table->string ('password');
             $table->boolean('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer1');
    }
};
