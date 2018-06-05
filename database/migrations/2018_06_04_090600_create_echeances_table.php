<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEcheancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('echeances', function (Blueprint $table) {
            $table->increments('id');
            $table->double('sommePayee');
            $table->double('sommeRestante');
            $table->date('dueDate');
            $table->unsignedInteger('addedBy');
            $table->unsignedInteger('document_id');
            $table->string('document_type');
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
        Schema::dropIfExists('echeances');
    }
}
