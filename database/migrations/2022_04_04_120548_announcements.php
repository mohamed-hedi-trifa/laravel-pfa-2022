<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Announcements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->Integer('idSeller')->unsigned();
            $table->foreign('idSeller')->references('id')->on('users')->onDelete('cascade');
            $table->string('nameAnn');
            $table->string('description');
            $table->string('images');
            $table->enum('gender', ['men', 'wommen','girls', 'boys']);
            $table->decimal('price', $precision = 8, $scale = 3);
            $table->integer('idCategory')->unsigned();
            $table->foreign('idCategory')->references('id')->on('categories')->onDelete('cascade');
            $table->integer('sumLike')->default(0);
            $table->integer('sumBasket')->default(0);
            $table->decimal('stars', $precision = 8, $scale = 2)->default(0);
            $table->enum('stock', ['yes', 'no'])->default('yes');
            $table->rememberToken();
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
        //
    }
}
