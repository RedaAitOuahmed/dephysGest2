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
            $table->double('prixVenteHT')->nullable();
            $table->float('TVA')->nullable();
            $table->double('prixVenteTTC')->nullable();
            $table->integer('addedBy');
            $table->boolean('estAchete')->nullable();
            $table->boolean('estVendu')->nullable();
            $table->string('description')->nullable();
            $table->unsignedInteger('categorie_id')->default(1);
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
