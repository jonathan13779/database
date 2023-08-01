<?php

namespace Jonathan13779\Database\Relation;

use Jonathan13779\Database\Model\Model;
use Jonathan13779\Database\Query\QueryBuilder;

abstract class Relation{

    public function __construct(
        protected string $relationType, 
        protected Model $fromModel, 
        protected string $fromKey, 
        protected Model $toModel, 
        protected string $toKey){
    }

    public function __invoke(): QueryBuilder
    {        
        $builder = new QueryBuilder();

        $builder->setRelation($this);
        
        $fromKey = $this->fromKey;
        $builder->where($this->toKey, '=', $this->fromModel->$fromKey);

        return $builder;
    }
    abstract public function getModel();
    abstract public function setJoin(QueryBuilder $queryBuilder): void;
    


}