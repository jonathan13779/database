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

    public function __invoke(?string $relationToProcess = null )
    {        
        $builder = new QueryBuilder();

        $builder->setRelation($this);
        
        $fromKey = $this->fromKey;
        $values = $this->fromModel->getKeysValues($fromKey);
        $builder->whereIn($this->toKey, $values);

        return $builder;
    }
    abstract public function getModel();
    abstract public function setJoin(QueryBuilder $queryBuilder): void;
    public function getFromKey(): string
    {
        return $this->fromKey;
    }
    
    public function getToKey(): string
    {
        return $this->toKey;
    }

    public function getFromModel()
    {
        return $this->fromModel;
    }


}