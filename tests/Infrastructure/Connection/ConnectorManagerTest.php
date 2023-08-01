<?php

use PHPUnit\Framework\TestCase;

use Jonathan13779\Database\Connection\ConnectorManager;
use Jonathan13779\Database\Connection\ConnectorDTO;


class ConnectorManagerTest extends TestCase
{
    /** @test */
    public function debe_incluir_nuevo_conector_al_gestor_conectores()
    {
        $connector = new ConnectorDTO(
            name: 'test',
            host: 'postgres',
            database: 'baviera',
            username: 'zataca',
            password: 'zataca'
        );
        ConnectorManager::addConnector($connector);
        $this->assertTrue(true);
    }

    /** @test */
    public function debe_realizar_la_conexion_a_la_db()
    {
        $connection = ConnectorManager::connect();
        $this->assertInstanceOf(PDO::class, $connection);
    }
}