<?php

namespace Jonathan13779\Database\Relation;

use Jonathan13779\Database\Model\Model;
use Jonathan13779\Database\Relation\Relation;
use Jonathan13779\Database\Query\QueryBuilder;

class ToOne extends Relation{

    public function __construct(
        Model $toModel, 
        string $toKey, 
        Model $fromModel, 
        string $fromKey){
            parent::__construct(
                'toOne',
                $fromModel,
                $fromKey,
                $toModel,
                $toKey
            );
    }

    public function getModel(): Model{
        return $this->toModel;
    }

    public function setJoin(QueryBuilder $queryBuilder): void{
        $queryBuilder->innerJoin(
            $this->fromModel->getTable(),
            $this->fromModel->getTable().'.'.$this->fromKey,
            '=',
            $this->toModel->getTable().'.'.$this->toKey
        );
    }

}