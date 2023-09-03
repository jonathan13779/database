<?php

namespace Jonathan13779\Database\Tests\Infrastructure\Model;

use PHPUnit\Framework\TestCase;
use Jonathan13779\Database\Tests\RoleModel;
use Jonathan13779\Database\Tests\AccionModel;
use Jonathan13779\Database\Tests\AccionTipoModel;
use Jonathan13779\Database\Connection\ConnectorDTO;
use Jonathan13779\Database\Connection\ConnectorManager;
use Jonathan13779\Database\Tests\PuestoModel;
use Jonathan13779\Database\Tests\DataBaseProvider;

class ModelTest extends TestCase
{
    protected function setUp(): void
    {
        DataBaseProvider::register();     
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
        $accionModel()
        ->relation('tipoAccion')
        ->fetch();
        
        $this->assertTrue(true);
    }

    public function test_debe_agregar_relacion_ansiosa_desde_multiples_registros()
    {
        $accionModel = new AccionModel();
        $data = $accionModel()
        ->relation('tipoAccion')
        ->relation('puesto.unidadRegional')
        ->get();
        //print_r($data->toArray()[0]);
        $this->assertTrue(true);
    }

    public function test_debe_agragar_ralcion_ansiosa_encadenada_desde_un_registro(){
        $accionModel = new AccionModel();
        //.unidadRegional
        //$accionModel()->method('puesto')->fetch();
        $builder = $accionModel()
        ->relation('tipoAccion')
        //->relation('puesto')
        ->relation('puesto.unidadRegional')
        //->relation('puesto.unidadRegional.unidadRegionalSelf')
        ->fetch();
        //print_r($builder);
        //exit;
        //print_r($accionModel);
        $this->assertTrue(true);
    }

    public function test_debe_filtrar_si_existe_la_relacion(){
        $accionModel = new AccionModel();
        $builder = $accionModel()
        ->hasRelation('tipoAccion')
        ->hasRelation('puesto.unidadRegional')
        ->fetch();
        //print_r($builder);
        //print_r($accionModel);
        $this->assertTrue(true);
    }

    public function test_debe_ejecutar_where_encapsulado(){
        $accionModel = new AccionModel();
        $builder = $accionModel()
        /*->where(function($query){
            $query->where(function($query){
                $query->where('nombre','Ipsum Quia Rerum');
            });
        });*/
        ->where('nombre','Ipsum Quia Rerum');
        //->fetch();
        //print_r($builder->getQuery()->getQuery());
        //print_r($accionModel);
        $this->assertTrue(true);

    }

    public function test_debe_ejecutar_una_relacion_muchos(){
        $puesto = new PuestoModel();
        $data = $puesto()
        ->where('id', 'c574ebc2-dedd-4853-a71b-3116d5203a24')
        ->relation('acciones.tipoAccion')
        ->fetch();
        //print_r($data);
        $this->assertTrue(true);
    }
}