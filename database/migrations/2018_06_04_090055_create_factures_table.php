<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('abandonnee')->default(false);
            $table->string('destNom');
            $table->string('destEmail');
            $table->string('destAdd');
            $table->string('destTel');
            $table->unsignedInteger('destId');
            $table->boolean('destAssujetiTVA');
            $table->string('destNumTVA');
            $table->double('reduction')->default(0); // indique le taux de réduction
            $table->boolean('reductionHT')->default(true); 
            /*vrai si la réduction dois s'appliquer sur le somme HT de la facture, 
            faux si elle doit s'appliquer sur la somme TTC de la facture.
            */
            $table->boolean('reductionParPourcentage')->default(false);
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
        Schema::dropIfExists('factures');
    }
}
