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
            $table->float('TVA');
            $table->double('prixAchat');
            $table->double('prixVente');
            $table->double('quantiteLivree');
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
