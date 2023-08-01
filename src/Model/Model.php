<?php
namespace Jonathan13779\Database\Model;

use Jonathan13779\Database\Query\QueryBuilder;
use Jonathan13779\Database\Relation\ToOne;

abstract class Model{
    protected string $table = '';
    protected $primaryKey = 'id';
    protected ?string $connection = null;

    public function __invoke(): QueryBuilder
    {
        $builder = new QueryBuilder();
        $builder->setModel($this);
        return $builder;
    }

    public function getTable(): string{
        return $this->table;
    }

    public function getConnection(): ?string{
        return $this->connection;
    }

    protected function toOne($toModel, $toKey, $fromKey){
        return (new ToOne(
            new $toModel(),
            $toKey,
            $this,
            $fromKey
        ))();
    }
}