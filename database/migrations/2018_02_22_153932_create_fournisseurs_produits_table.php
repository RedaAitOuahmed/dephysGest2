<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFournisseursProduitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fournisseurs_produits', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('produit_id');
            $table->unsignedInteger('fournisseur_id');
            $table->float('prixAchatHT')->nullable();
            $table->float('TVA_achat')->nullable();
            $table->float('prixAchatTTC')->nullable();
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
        Schema::dropIfExists('fournisseurs_produits');
    }
}
