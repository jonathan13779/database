<?php

namespace Jonathan13779\Database\Tests\Infrastructure\Model;

use PHPUnit\Framework\TestCase;
use Jonathan13779\Database\Tests\RoleModel;
use Jonathan13779\Database\Tests\AccionModel;
use Jonathan13779\Database\Tests\AccionTipoModel;
use Jonathan13779\Database\Connection\ConnectorDTO;
use Jonathan13779\Database\Connection\ConnectorManager;

class ModelTest extends TestCase
{
    protected function setUp(): void
    {
        $connector = new ConnectorDTO(
            name: 'test',
            host: 'postgres',
            database: 'baviera',
            username: 'zataca',
            password: 'zataca'
        );
        ConnectorManager::addConnector($connector);        
        parent::setUp();
    }    
    public function test_model()
    {
        $roleModel = new RoleModel();
        $data = $roleModel()->fetch();
        //echo $roleModel->id;
        $this->assertTrue(true);
    }

    public function test_debe_hacer_una_relacion_a_uno()
    {
        $accionModel = new AccionModel();
        $relation = $accionModel()->fetch()->tipoAccion()->fetch();
        //echo $relation;
        //exit;
        print_r($relation);
        $this->assertTrue(true);
    }
}