<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->integer('id_reg');
            $table->string('description');
            $table->timestamps();
            $table->primary('id_reg');
        });

        DB::statement("
            ALTER TABLE
            regions
            ADD COLUMN
            status enum(
                'A', 
                'I', 
                'trash'
            )
            NOT NULL DEFAULT 'A' COMMENT 'estado del registro: A: ActivoI : Desactivo  trash : Registro eliminado';
        "); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('regions');
    }
}
