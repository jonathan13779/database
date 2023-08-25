<?php

namespace Jonathan13779\Database\Query;

use Jonathan13779\Database\Model\Model;
use Jonathan13779\Database\Model\Collection;
use Jonathan13779\Database\Query\SqlBuilder;
use Jonathan13779\Database\Connection\ConnectorManager;
use Jonathan13779\Database\Relation\Relation;
use PDO;
use PDOStatement;

class QueryBuilder{
    private Model $model; 
    private Relation $relation;

    private array $selectArr = ['*'];
    private array $joinArr = [];
    private array $whereArr = [];
    private array $methods = [];
    private array $relations = [];
    private array $hasRelations = [];

    public function __construct(){
    }

    public function __toString()
    {
        return $this->getQuery();    
    }

    public function setModel(Model $model): QueryBuilder{
        $this->model = $model;
        return $this;
    }

    public function setRelation(Relation $relation): QueryBuilder{
        $this->relation = $relation;
        $this->model = $relation->getModel();

        return $this;
    }

    public function getSelect(): array{
        return $this->selectArr;
    }

    public function getTable(): string{
        return $this->model->getTable();
    }

    public function getJoin(): array{
        return $this->joinArr;
    }

    public function getWhere(): array{
        return $this->whereArr;
    }
    

    public function select (...$params): QueryBuilder{
        $this->selectArr = $params;
        return $this;
    }

    public function innerJoin($table, $fromField, $operator, $tofield): QueryBuilder{
        $this->joinArr[] = [
            'type' => 'inner',
            'table' => $table,
            'fromField' => $fromField,
            'operator' => $operator,
            'toField' => $tofield
        ];
        return $this;
    }

    public function where(...$params): QueryBuilder{
        $field = $params[0];
        $operator = '=';
        if (isset($params[1])){
            $value = $params[1];
        }
        

        if (is_callable($field)){
            $this->whereArr[] = [
                'callback' => $field
            ];
            return $this;
        }

        if(count($params) === 3){
            $operator = $params[1];
            $value = $params[2];
        }
        $this->whereArr[] = [
            'field' => $field,
            'operator' => $operator,
            'value' => $value
        ];
        return $this;
    }

    public function whereSql(string $sql, array $params = []): QueryBuilder{
        $this->whereArr[] = [
            'sql' => $sql,
            'params' => $params
        ];
        return $this;
    }

    public function whereIn($field, array $values): QueryBuilder{
        $this->whereArr[] = [
            'field' => $field,
            'operator' => 'IN',
            'value' => $values
        ];
        return $this;
    }

    public function method(string $method): QueryBuilder{
        $this->methods[] = $method;
        return $this;
    }

    public function relation(string $relation): QueryBuilder
    {
        $relations = explode('.', $relation);
        $node = &$this->relations;
        foreach($relations as $relation){
            if(!isset($node[$relation])){
                if (isset($node['childs'])){
                    $node['childs'][$relation] = [
                        'name' => $relation,
                        'childs' => []
                    ];
                    $node = &$node['childs'][$relation];
                }
                else{
                    $node[$relation] = [
                        'name' => $relation,
                        'childs' => []
                    ];
                    $node = &$node[$relation];

    
                }
            }
            else{
                $node = &$node[$relation];

            }
        }
        
        return $this;
    }


    public function hasRelation(string $relation): QueryBuilder
    {
        $relations = explode('.', $relation);
        $node = &$this->hasRelations;
        foreach($relations as $relation){
            if(!isset($node[$relation])){
                if (isset($node['childs'])){
                    $node['childs'][$relation] = [
                        'name' => $relation,
                        'childs' => []
                    ];
                    $node = &$node['childs'][$relation];
                }
                else{
                    $node[$relation] = [
                        'name' => $relation,
                        'childs' => []
                    ];
                    $node = &$node[$relation];

    
                }
            }
            else{
                $node = &$node[$relation];

            }
        }
        
        return $this;
    }

    
    public function fetch(){

        $stmt = $this->prepareStatement();

        $data = $stmt->fetch();
        $this->model->setRow($data);
        
        foreach($this->relations as $relation){
            $this->model->prepareRelation($relation);
        }

        return $this->model;
    }

    public function get(){
        $stmt = $this->prepareStatement();

        $data = $stmt->fetchAll();
        $this->model->setRows($data);
        $collection = new Collection();
        foreach($data as $row){
            
            $class = get_class($this->model);
            $model = new $class();
            $model->setRow($row);
            $collection->add($model);
        }
        $this->model->setCollection($collection);
        
        foreach($this->relations as $relation){
            $this->model->prepareRelation($relation);
        }
        return $collection;
    }


    public function getRelation(){
        $stmt = $this->prepareStatement();

        $data = $stmt->fetchAll();
        $collection = new Collection();
        foreach($data as $row){
            $class = get_class($this->model);
            $model = new $class();
            $model->setRow($row);
            $collection->add($model);
        }
        $this->model->setCollection($collection);
 

        foreach($this->relations as $relation){
            $this->model->prepareRelation($relation);
        }
        
        
        foreach($this->methods as $method){
            $this->model->executeMethod($method);
        }
        return $this->model;

    }

    public function getArray()
    {
        $stmt = $this->prepareStatement();
        $data = $stmt->fetchAll();
        return $data;
    }

 
    private function prepareStatement(): PDOStatement
    {        
        $sql = $this->getQuery();
        $connection = $this->model->getConnection();
        $pdo = ConnectorManager::connect($connection);
        $stmt = $pdo->prepare($sql);
        
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        
        $stmt->execute($sql->getParams());
        return $stmt;
    }

    public function sql(){
        echo $this->getQuery();
        exit;
    }

    public function getQuery(): SqlBuilder{
        $this->getFilterRelation();
        return new SqlBuilder($this);
    }

    public function getFilterRelation(){
        /*$model = $this->relation->getModel();;
        $table = $model->getTable();
        $fromModel = $this->relation->getFromModel();
        $fromTable = $fromModel->getTable();
        $fromKey = $this->relation->getFromKey();
        $toKey = $this->relation->getToKey();
        $sql = "
        select * 
        from $table where 
        where $fromTable.$fromKey = $table.$toKey
        ";
        echo $sql;
        exit;*/
        $sql = '';
        
        foreach($this->hasRelations as $hasRelation){
            $sql = $this->model->getFilterRelation($hasRelation, $this);
        }
        return $sql;

    }

}