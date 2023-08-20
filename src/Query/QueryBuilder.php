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
        $value = $params[1];
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

    public function fetch(){

        $stmt = $this->prepareStatement();

        $data = $stmt->fetch();
        $this->model->setRow($data);
        
        foreach($this->methods as $method){
            $this->model->executeMethod($method);

            /*
            $methodResult = $this->model->{$method}();
            $this->model->addMethod($method, $methodResult);
            */
        }

        return $this->model;
    }

    public function get(){
        $stmt = $this->prepareStatement();

        $data = $stmt->fetchAll();
        $this->model->setRows($data);
        $collection = new Collection();
        
        foreach($this->methods as $method){
            $this->model->processRelation($method);
            //$methodResult = $this->model->{$method}($method);
            //$this->model->addMethod($method, $methodResult);
        }

        foreach($data as $row){
            $class = get_class($this->model);
            $model = new $class();
            $model->setRow($row);

            foreach($this->methods as $method){
                $relatedItem = $this->model->getProcessedRelation($row, $method);
                $model->addMethod($method, $relatedItem);   
            }
            $collection->add($model);
            
        }
        return $collection;

        //preparar la coleccion con los rows de la relacion

        return $this->model;
        
    }


    public function getRelation(){
        $stmt = $this->prepareStatement();

        $data = $stmt->fetchAll();
        //$this->model->setRows($data);
        $collection = new Collection();
        foreach($data as $row){
            $class = get_class($this->model);
            $model = new $class();
            $model->setRow($row);
            $collection->add($model);
        }
        $this->model->setCollection($collection);
        
        
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
        echo new SqlBuilder($this);
        exit;
    }

    public function getQuery(): SqlBuilder{
        return new SqlBuilder($this);
    }
}