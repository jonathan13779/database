<?php
namespace Jonathan13779\Database\Query;

use Jonathan13779\Database\Query\QueryBuilder;

class SqlBuilder{

    private array $sqlArr = [];
    private array $params = [];

    public function __construct(private QueryBuilder $queryBuilder)
    {
    }

    public function __toString(): string{
        return $this->getQuery();
    }

    public function getQuery(): string{
        $this->sqlArr = [];
        $this->select();
        $this->from();
        $this->join();

        $this->where();
        $query = implode(' ', $this->sqlArr);
        return $query;
    }


    private function select(): void{
        $select = $this->queryBuilder->getSelect();

        if(count($select) === 0){
            $this->select = ['*'];
        }
        $this->sqlArr[] = 'SELECT ' . implode(', ', $select);
    }

    private function from(): void{
        $this->sqlArr[] = 'FROM ' . $this->queryBuilder->getTable();
    }

    private function join(): void{
        $joinArr = $this->queryBuilder->getJoin();

        if(count($joinArr) > 0){
            foreach($joinArr as $join){
                $this->sqlArr[] = $join['type'] . ' JOIN ' . $join['table'] . ' ON ' . $join['fromField'] . ' ' . $join['operator'] . ' ' . $join['toField'];
            }
        }

    }

    private function where(): void{
        $whereArr = $this->queryBuilder->getWhere();

        if(count($whereArr) > 0){
            $this->sqlArr[] = 'WHERE ';
            foreach($whereArr as $where){
                if ($where['operator'] == 'IN'){
                    $this->whereIn($where['field'], $where['value']);                    
                    continue;
                }
                $this->sqlArr[] = $where['field'] . ' ' . $where['operator'] . ' ?';
                $this->params[] = $where['value'];
            }
        }
    }

    private function whereIn($field, $values): void{
        foreach($values as $value){
            $this->sqlValues[] = '?';
            $this->params[] = $value;
        }
        $this->sqlArr[] = $field . ' IN (' . implode(', ', $this->sqlValues) . ')';
    }
    
    public function getParams(): array{
        return $this->params;
    }

}