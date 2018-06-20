<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nom');
            $table->float('TVA_vente');
            $table->double('prixVenteHT');
            $table->float('TVA_achat');
            $table->double('prixAchatHT');
            $table->double('prixAchatTTC');
            $table->double('reduction');
            // indique le taux de réduction
            $table->boolean('reductionHT'); 
            /*vrai si la réduction dois s'appliquer sur le prix de vente HT de ce produit, 
            faux si elle doit s'appliquer sur le prix de vente TTC du produit.
            */
            $table->boolean('reductionParPourcentage');
            /*
            vrai si la réducton est un pourcentage, faux si la réduction est un montant à déduire.
            */
            $table->double('quantite')->default(1.00);
            $table->double('quantiteLivree')->default(0.00);
            $table->string('unite'); // kgs ou pieces ou ...
            $table->unsignedInteger('document_id');
            $table->string('document_type');
            $table->unsignedInteger('produit_id');
            $table->string('details');
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
        Schema::dropIfExists('document_entries');
    }
}
