<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('destNom');
            $table->string('destEmail');
            $table->string('destAdd');
            $table->string('destTel');
            $table->unsignedInteger('destId');
            $table->double('reduction'); // indique le taux de réduction
            $table->boolean('reductionHT'); 
            /*vrai si la réduction dois s'appliquer sur le somme HT de la commande, 
            faux si elle doit s'appliquer sur la somme TTC de la commande.
            */
            $table->boolean('reductionParPourcentage');
            /*
            vrai si la réducton est un pourcentage, faux si la réduction est un montant à déduire.
            */
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
        Schema::dropIfExists('commandes');
    }
}
