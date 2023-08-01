<?php

namespace Jonathan13779\Database\Query;

use Jonathan13779\Database\Model\Model;
use Jonathan13779\Database\Query\SqlBuilder;
use Jonathan13779\Database\Connection\ConnectorManager;
use Jonathan13779\Database\Relation\Relation;
use PDO;

class QueryBuilder{
    private Model $model; 
    private Relation $relation;

    private array $selectArr = ['*'];
    private array $joinArr = [];
    private array $whereArr = [];

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

    public function fetch(){
        $sql = $this->getQuery();
        $connection = $this->model->getConnection();
        $pdo = ConnectorManager::connect($connection);
        $stmt = $pdo->prepare($sql);
        $stmt->setFetchMode( PDO::FETCH_INTO, $this->model);
        $stmt->execute($sql->getParams());
        
        return $stmt->fetch();
    }

    public function sql(){
        echo new SqlBuilder($this);
        exit;
    }

    public function getQuery(): SqlBuilder{
        return new SqlBuilder($this);
    }
}