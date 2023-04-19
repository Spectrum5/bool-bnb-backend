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
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->unique();
            $table->string('slug', 255)->unique();
            $table->smallInteger('price');
            $table->string('image', 255);
            $table->boolean('visibility')->default('1');
            
            // Address
            $table->string('address', 512);
            $table->double('lat', 10, 7);
            $table->double('lng', 10, 7);

            // Details
            $table->tinyInteger('rooms_number')->unsigned();
            $table->tinyInteger('bathrooms_number')->unsigned();
            $table->tinyInteger('beds_number')->unsigned();
            $table->text('description');
            $table->smallInteger('size')->unsigned();
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
        Schema::dropIfExists('apartments');
    }
};