<?php

namespace Jonathan13779\Database\Tests;

use Jonathan13779\Database\Model\Model;
use Jonathan13779\Database\Tests\UnidadRegionalModel;

class PuestoModel extends Model{

    protected string $table = 'puestos';
    
    public function unidadRegional()
    {
        return $this->toOne(UnidadRegionalModel::class, 'id', 'fk_unidad_regional_id');
    } 
}