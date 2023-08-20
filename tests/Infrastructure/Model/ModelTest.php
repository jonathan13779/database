<?php

namespace Jonathan13779\Database\Tests\Infrastructure\Model;

use PHPUnit\Framework\TestCase;
use Jonathan13779\Database\Tests\RoleModel;
use Jonathan13779\Database\Tests\AccionModel;
use Jonathan13779\Database\Tests\AccionTipoModel;
use Jonathan13779\Database\Connection\ConnectorDTO;
use Jonathan13779\Database\Connection\ConnectorManager;
use Jonathan13779\Database\Tests\PuestoModel;

class ModelTest extends TestCase
{
    protected function setUp(): void
    {
        $connector = new ConnectorDTO(
            name: 'test',
            host: 'postgres',
            database: 'db',
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
        $relation = $accionModel()->fetch()->tipoAccion();
        //var_dump($relation);
        $this->assertTrue(true);
    }

    public function test_debe_agregar_relacion_ansiosa_a_uno()
    {
        $accionModel = new AccionModel();
        $accionModel()->method('tipoAccion')->fetch();
        
        $this->assertTrue(true);
    }

    public function test_debe_agregar_relacion_ansiosa_a_uno_desde_multiples_registros()
    {
        $accionModel = new AccionModel();
        $data = $accionModel()->method('tipoAccion')->get();
        var_dump($data->toArray()[0]);
        $this->assertTrue(true);
    }

    public function test_debe_agragar_ralcion_ansiosa_encadenada_desde_un_registro(){
        $accionModel = new AccionModel();
        //.unidadRegional
        //$accionModel()->method('puesto')->fetch();
        $accionModel()->method('tipoAccion')->method('puesto')->method('puesto.unidadRegional')->fetch();
        print_r($accionModel);
        $this->assertTrue(true);
    }
}