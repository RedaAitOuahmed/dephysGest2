<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntreprisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entreprises', function (Blueprint $table) {
            $table->increments('id');
            $table->string('siren')->nullable();
            $table->string('siret')->nullable();
            $table->boolean('assujettiTVA')->nullable();
            $table->string('numTVA')->nullable();
            $table->timestamps();
        });
        // Schema::table('entreprises', function($table){
        //     $table->foreign('id')->references('contactable_id')->on('contacts');
            
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entreprises');
    }
}
