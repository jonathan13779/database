<?php

namespace Jonathan13779\Database\Tests;

use Jonathan13779\Database\Model\Model;
use Jonathan13779\Database\Tests\AccionTipoModel;

class AccionModel extends Model{

    protected string $table = 'acciones';
    
    public function tipoAccion(){
        return $this->toOne(
            AccionTipoModel::class,
            'id',
            'fk_tipo_id'
        );
    }    
}