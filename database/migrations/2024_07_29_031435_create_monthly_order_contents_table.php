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
        Schema::create('monthly_order_contents', function (Blueprint $table) {
            $table->uuid("id")->primary();
            // $table->foreignId("monthly_order_id")->constrained("monthly_orders" ,"id")->cascadeOnDelete();
            $table->foreignUuid("monthly_order_id")->constrained("monthly_orders","id")->cascadeOnDelete();
            $table->foreignId("category_id")->constrained("categories" ,"id")->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_order_contents');
    }
};
