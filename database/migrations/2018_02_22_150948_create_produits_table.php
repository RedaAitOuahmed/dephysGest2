<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProduitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nom')->nullable();
            $table->string('codeBarre')->nullable();
            $table->float('prixAchat')->nullable();
            $table->float('prixVente')->nullable();
            $table->float('TVA')->nullable();
            $table->integer('addedBy');
            $table->boolean('estAchete')->nullable();
            $table->boolean('estVendu')->nullable();
            $table->string('description')->nullable();
            $table->integer('categorie_id');
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
        Schema::dropIfExists('produits');
    }
}
