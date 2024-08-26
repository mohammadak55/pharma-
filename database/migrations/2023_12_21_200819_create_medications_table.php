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
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId("warehouse_id")->constrained("warehouses");
            $table->string("scientific name");
            $table->string("Trade Name");
            $table->string("Manufacturer");
            $table->longText("ImagePath");
            $table->bigInteger("Available quantity");
            $table->date("Expiry date");
            $table->enum("expired" , ["available" , "expired"])->default("available");
            $table->bigInteger("Price");
            $table->foreignId("category_id")->constrained("categories");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
