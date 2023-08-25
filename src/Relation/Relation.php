<?php

namespace Jonathan13779\Database\Relation;

use Jonathan13779\Database\Model\Model;
use Jonathan13779\Database\Query\QueryBuilder;
use Jonathan13779\Database\Model\ProcessedRelation;

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

    public function getType(): string{
        return $this->relationType;
    }

    public function getQueryBuilder(): QueryBuilder{
        $builder = new QueryBuilder();
        $builder->setRelation($this);
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

    public function addRelations(array $fromRelations): void{
        $queryBuilder = self::__invoke();
        $childs = $fromRelations['childs'];
        $relations = [];
        foreach($childs as $child){
            $name = $child['name'];
            $relations[] = $name;
            $this->concatChildsRelations($relations, $name, $child['childs']);
            
        }
        foreach($relations as $relation){
            $queryBuilder->relation($relation);
        }
        $queryBuilder->getRelation();
        new ProcessedRelation($fromRelations['name'], $this);
    }

    private function concatChildsRelations(&$relations, &$name, $childs): void{
        foreach($childs as $child){
            $name .= '.'.$child['name'];
            $relations[] = $name;
            $this->concatChildsRelations($relations, $name, $child['childs']);
        }
    }

    public function getFilterExists(array $fromHasRelation, $queryBuilder){
        $model = $this->getModel();
        $queryBuilderModel = $model();
        $table = $model->getTable();
        $fromModel = $this->getFromModel();
        $fromTable = $fromModel->getTable();
        $fromKey = $this->getFromKey();
        $toKey = $this->getToKey();
        
        $queryBuilderModel->whereSql("$fromTable.$fromKey = $table.$toKey");
        
        $relations = [];
        $childs = $fromHasRelation['childs'];

        foreach($childs as $child){
            $name = $child['name'];
            $relations[] = $name;
            $this->concatChildsRelations($relations, $name, $child['childs']);
        }
        
        foreach($relations as $relation){
            $queryBuilderModel->hasRelation($relation);
        }     
    
        $subQuery = $queryBuilderModel->getQuery()->getQuery();
        $subQuery = 'exists ('.$subQuery.')';

        $queryBuilder->whereSql($subQuery);
    }

}