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
        Schema::create('order_contents', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("order_id")->constrained("orders","id")->cascadeOnDelete();
            $table->foreignId("food_id")->constrained("foods" ,"id")->cascadeOnDelete();
            $table->timestamps();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_contents');
    }
};
