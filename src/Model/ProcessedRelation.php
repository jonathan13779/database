<?php
namespace Jonathan13779\Database\Model;

use Jonathan13779\Database\Relation\Relation;

class ProcessedRelation
{

    private array $dataByKey = [];
    private $fromKey;
    private $toKey;
    public function __construct(
        public readonly string $name, 
        Relation $relation, 
        )
    {
        $this->fromKey = $relation->getFromKey();
        $this->toKey = $relation->getToKey();

        $model = ($relation->getModel());
        $data = $model->getCollection();
        foreach ($data as $row) {
            $keyName = $relation->getToKey();
            $keyValue = $row->{$keyName};
    
            $this->dataByKey[$keyValue] = $row;
        }
        
        $fromModel = $relation->getFromModel();
        $fromModel->mergeRelation($this);

        //$fromModel->merge[$name] = $relation->getModel();
        //var_dump($relation->getFromModel());
        //exit;
    }

    public function get($key)
    {
        return $this->dataByKey[$key] ?? null;
    }

    public function getFromKey(): string
    {
        return $this->fromKey;
    }

    public function getToKey(): string
    {
        return $this->toKey;
    }

}