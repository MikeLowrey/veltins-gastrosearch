<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlacesItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places_items', function (Blueprint $table) {
            $table->id();
            $table->string('place_id')->unique();
            $table->text('location')->nullable();
            $table->string('name')->nullable();
            $table->string('types')->nullable();
            $table->string('place')->nullable();
            $table->string('zip')->nullable();
            $table->string('street')->nullable();
            $table->string('street_number')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();   
            $table->string('formatted_address')->nullable();               
            $table->integer('user_ratings_total')->default(0);                           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('places_items');
    }
}
