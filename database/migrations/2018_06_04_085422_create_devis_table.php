<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devis', function (Blueprint $table) {
            $table->increments('id');
            $table->string('statut');
            $table->string('destNom');
            $table->string('destEmail');
            $table->string('destAdd');
            $table->string('destTel');
            $table->unsignedInteger('destId');
            $table->boolean('destAssujetiTVA');
            $table->string('basDePage');
            $table->unsignedInteger('addedBy');
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
        Schema::dropIfExists('devis');
    }
}
