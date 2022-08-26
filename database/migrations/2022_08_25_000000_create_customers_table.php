<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->string('dni')->nullable(false)->unique();
            $table->integer('id_reg');
            $table->integer('id_com');
            $table->string('email')->unique();
            $table->string('name');
            $table->string('last_name');
            $table->string('address');
            $table->timestamp('date_reg');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->primary(['dni', 'id_reg', 'id_com']);
            $table->index(['email']);
        });

        DB::statement("
            ALTER TABLE
            customers
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
        Schema::dropIfExists('customers');
    }
}
