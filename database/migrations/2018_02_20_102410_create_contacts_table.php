<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nom');
            $table->string('relation')->nullable();
            $table->string('email')->nullable();
            $table->string('tel')->nullable();
            $table->string('adresse')->nullable();
            $table->string('fax')->nullable();
            $table->string('contactable_type')->nullable();
            $table->integer('contactable_id')->nullable();
            $table->integer('added_by');
            $table->timestamps();
        });
        Schema::table('contacts', function($table){
            $table->unique(['contactable_type','email']);
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
