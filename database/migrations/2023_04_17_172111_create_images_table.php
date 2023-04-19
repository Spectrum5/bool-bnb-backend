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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('url', 255);
            $table->unsignedBigInteger('apartment_id');
            $table->foreign('apartment_id')
                ->references('id')
                ->on('apartments')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
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
        Schema::dropIfExists('images');

        Schema::table('images', function (Blueprint $table) {
            $table->dropForeign(['apartment_id']);
            $table->dropColumn('apartment_id');
        });
    }
};