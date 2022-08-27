<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            INSERT INTO regions (id_reg, description, created_at)
            VALUES
            (1,'Amazonas', NOW()),
            (2,'Antioquia', NOW()),
            (3,'Arauca', NOW()),
            (4,'Atlántico', NOW()),
            (5,'Bolívar', NOW()),
            (6,'Boyacá', NOW()),
            (7,'Caldas', NOW()),
            (8,'Caquetá', NOW()),
            (9,'Casanare', NOW()),
            (10,'Cauca', NOW()),
            (11,'Cesar', NOW()),
            (12,'Chocó', NOW()),
            (13,'Córdoba', NOW()),
            (14,'Cundinamarca', NOW()),
            (15,'Güainia', NOW()),
            (16,'Guaviare', NOW()),
            (17,'Huila', NOW()),
            (18,'La Guajira', NOW()),
            (19,'Magdalena', NOW()),
            (20,'Meta', NOW()),
            (21,'Nariño', NOW()),
            (22,'Norte de Santander', NOW()),
            (23,'Putumayo', NOW()),
            (24,'Quindo', NOW()),
            (25,'Risaralda', NOW()),
            (26,'San Andrés y Providencia', NOW()),
            (27,'Santander', NOW()),
            (28,'Sucre', NOW()),
            (29,'Tolima', NOW()),
            (30,'Valle del Cauca', NOW()),
            (31,'Vaupés', NOW()),
            (32,'Vichada', NOW());
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
