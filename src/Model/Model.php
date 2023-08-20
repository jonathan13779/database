<?php
namespace Jonathan13779\Database\Model;

use Jonathan13779\Database\Query\QueryBuilder;
use Jonathan13779\Database\Relation\ToOne;
use Jonathan13779\Database\Model\ProcessedRelation;

abstract class Model{
    protected string $table = '';
    protected $primaryKey = 'id';
    protected ?string $connection = null;

    protected ?array $row = null;
    protected ?array $rows = null;
    protected ?Collection $collection = null;
    protected array $methods = [];
    protected bool $autoInvoke = true;
    protected array $processedRelations = [];

    public function __invoke(): QueryBuilder
    {
        $builder = new QueryBuilder();
        $builder->setModel($this);
        return $builder;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->row)) {
            return $this->row[$name];
        }
    }

    public function getTable(): string{
        return $this->table;
    }

    public function getConnection(): ?string{
        return $this->connection;
    }

    protected function toOne($toModel, $toKey, $fromKey){
        $relation = new ToOne(
            new $toModel(),
            $toKey,
            $this,
            $fromKey
        );
        if ($this->autoInvoke) {
            return $relation();
        }
        return $relation;
    }

    public function setRow(?array $row): void{
        $this->row = $row;
    }

    public function setRows(array $rows): void{
        $this->rows = $rows;
    }

    public function getCollection(): Collection{
        return $this->collection;
    }

    public function setCollection(Collection $collection): void{
        $this->collection = $collection;
    }

    public function addMethod(string $name, $method): void{
        $this->methods[$name] = $method;
    }

    public function getKeysValues(string $field): array{
        if (!is_null($this->rows) || !is_null($this->collection)) {
            return $this->getKeysFromArray($field);
        }

        return $this->getKeysFromRow($field);
    }

    public $merge = [];

    public function executeMethod(string $method){
        $chain = explode('.', $method);
        $first = $chain[0];
        $next = null;
        if (count($chain) > 1){
            $next = $chain[1];
        }
        $this->autoInvoke = false;

        $relation = $this->$first()($first, $next);
        
    
        //$this->processedRelations[$first] = $relation;
        //$this->merge[$first] = true;
        //$relation->method($chain[1]);
        //if ($first=='puesto')
        {
            /*echo static::class."\n";
            echo "\npasa***************************\n";
            //var_dump($this);
            var_dump($relation);
            exit;    */    
    
        }

        $this->autoInvoke = true;
    }

    public function processRelation($relation){
        $this->autoInvoke = false;
        $result = $this->$relation()($relation);
        $this->processedRelations[$relation] = $result;
        $this->autoInvoke = true;
    }

    public function getProcessedRelation($row, $relationName){
        $relation = $this->processedRelations[$relationName];
        $relationKey = $relation->getFromKey();
        $keyValue = $row[$relationKey];
        $data = $relation->get($keyValue);
        return $data;
    }

    public function mergeRelation(ProcessedRelation $relation){
        if ($this->row){
            $this->mergeRelationToRow($relation);
        }
        else{
            $this->mergeRelationToRows($relation);
        }
    }

    private function mergeRelationToRow(ProcessedRelation $relation){

        $relationKey = $relation->getFromKey();
        $keyValue = $this->row[$relationKey];
        $data = $relation->get($keyValue);
        //print_r($relation);
        $this->addMethod($relation->name, $data);
    }

    private function mergeRelationToRows(ProcessedRelation $relation){
        $relationKey = $relation->getFromKey();
        $rows = $this->collection;
        foreach($rows as &$row){
            $keyValue = $row->{$relationKey};
            $data = $relation->get($keyValue);
            $row->addMethod($relation->name, $data);
            //$row->merge[$relation->name] = $data;
            
        }

    } 

    private function getKeysFromArray(string $field): array{
        if (!is_null($this->collection)) {
            return $this->getKeysFromCollection($field);
        }
        $keys = [];
        foreach($this->rows as $row){
            $keys[] = $row[$field];
        }
        return $keys;
    }

    private function getKeysFromCollection(string $field): array{
        $keys = [];
        foreach($this->collection as $item){
            $keys[] = $item->{$field};
        }
        return $keys;
    }

    private function getKeysFromRow(string $field): array{
        return [$this->row[$field]];
    }
}