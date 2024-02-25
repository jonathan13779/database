<?php

namespace Jonathan13779\Database\Tests\Infrastructure\Migration;

use Jonathan13779\Database\Migration\Migration;
use Jonathan13779\Database\Migration\Schema;
use Jonathan13779\Database\Tests\DataBaseProvider;
use PHPUnit\Framework\TestCase;

class MigrationTest extends TestCase
{

    protected function setUp(): void
    {
        DataBaseProvider::register();     
        parent::setUp();
    }    


    public function test_debe_generar_nuevo_fichero_migracion()
    {
        Migration::create('prueba');
        $this->assertTrue(true);
    }

    public function test_debe_ejecutar_migraciones()
    {
        Migration::remove();
        Schema::reset();
        Migration::create('prueba');
        Migration::execute();
        $this->assertTrue(true);
    }

    public function test_debe_crear_una_tabla()
    {
        Schema::reset();
        Schema::createTable('prueba', function($table){
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email');
            $table->integer('age')->nullable();
            $table->index('name');
            $table->jsonb('data');

        });

        Schema::createTable('prueba2', function($table){
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email');
            $table->integer('age')->nullable();
            $table->jsonb('data');
            $table->foreignKey('id')->references('prueba', 'id');

        });

        $this->assertTrue(true);
    }

}