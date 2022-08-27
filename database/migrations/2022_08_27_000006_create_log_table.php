<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log', function (Blueprint $table) {
            $table->id();
            $table->string('ip')->nullable(true);
            $table->string('input', 1000)->nullable(true);
            $table->string('output', 1000)->nullable(true);
            $table->datetime('created_at')->nullable(true);
        });
        DB::statement("
            ALTER TABLE
            log
            ADD COLUMN
            tipo INT
            NOT NULL DEFAULT 0 COMMENT '0 entrada 1 salida';
        "); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log');
    }
}
