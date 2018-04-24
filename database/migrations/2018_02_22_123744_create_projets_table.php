<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nom')->unique();
            $table->string('description')->nullable();
            $table->integer('addedBy')->nullable();
            $table->timestamps();
        });
        //creating a first default row

        DB::table('projets')->insert(
                array(
                    'nom' => 'Libre',
                    'description' => "Ce Projet contient toutes les tâches qui n'appartienent à aucun autre projet",
                    'addedBy' => null,
                )
            );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projets');
    }
}
