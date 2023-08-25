<?php

namespace Jonathan13779\Database\Tests;

use Jonathan13779\Database\Model\Model;

class UnidadRegionalModel extends Model{

    protected string $table = 'unidades_regionales';

    public function unidadRegionalSelf(){
        return $this->toOne(UnidadRegionalModel::class, 'id', 'id');
    }
      
}