<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_listing_id')->nullable()->constrained('listings')->onDelete('cascade');
            $table->foreignId('child_listing_id')->nullable()->constrained('listings')->onDelete('cascade');
            $table->timestamps();
            
            // Prevent duplicate relationships
            $table->unique(['parent_listing_id', 'child_listing_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listing_listings');
    }
};
