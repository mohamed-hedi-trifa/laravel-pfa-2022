<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Categories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments("id");
            $table->string('name');
            $table->timestamps();
        });
    }/*
  $table->increments("id");
            $table->integer('idAnnouncement')->unsigned();
            $table->foreign('idAnnouncement')->references('id')->on('annoucements')->onDelete('cascade');
            $table->string('url');
            $table->timestamps();
    */

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
