<?php
namespace Jonathan13779\Database\Query;

use Jonathan13779\Database\Query\QueryBuilder;

class SqlBuilder{

    private array $sqlArr = [];
    private array $conditions = [];
    private array $conditionsArr = [];
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
        
        $where = $this->queryBuilder->getWhere();
        if(count($where) > 0){
            $this->sqlArr[] = 'WHERE ';
            $this->buildConditions($this->conditionsArr);
            $this->where($this->sqlArr, $this->conditionsArr);
        }
        
        $query = implode(' ', $this->sqlArr);
        return $query;
    }

    public function buildConditions(&$conditions): void{
        $whereArr = $this->queryBuilder->getWhere();
        foreach($whereArr as $where){

            if (isset($where['callback'])){                
                $this->exeecuteCallbackInWhere($where['callback'], $conditions);
                continue;                        
            }
            if (isset($where['sql'])){
                $conditions[] = [
                    'operator' => 'AND',
                    'condition'=> $where['sql'],
                    'params' => $where['params']
                ];
                continue;
            }
            if ($where['operator'] == 'IN'){
                $this->whereInCondition($where['field'], $where['value'], $conditions);                    
                continue;
            }

            $conditions[] = [
                'operator' => 'AND',
                'condition'=> $where['field'] . ' ' . $where['operator'] . ' ?',
                'params' => [$where['value']]
            ];
        }
    }

    private function where(&$sqlArr, $conditions): void
    {
        $addedCondition = false;
        foreach($conditions as $condition){
            $operator = '';
            if ($addedCondition){
                $operator = $condition['operator'];
            }
            if (isset($condition['group'])){
                $sqlArr[] = $operator . ' (';
                $this->where($sqlArr, $condition['group']);
                $sqlArr[] = ')';
                $addedCondition = true;
                continue;

            }
            $sqlArr[] = $operator .' '. $condition['condition'];
            $this->params = array_merge($this->params, $condition['params']);
            $addedCondition = true;
        }

    }

    private function whereInCondition($field, $values, &$conditions): void{
        $params = [];
        $sqlValues = [];
        foreach($values as $value){
            $sqlValues[] = '?';
            $params[] = $value;
        }
        $conditions[]= [
            'operator' => 'AND',
            'condition'=> $field . ' IN (' . implode(', ', $sqlValues) . ')',
            'params' => $params
        ];
    }


    public function getConditions(): array{
        return $this->conditions;
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

    public function whereOld(&$sqlArr): void{
        $whereArr = $this->queryBuilder->getWhere();
        $this->conditions = [];
        if(count($whereArr) > 0){
            
            foreach($whereArr as $where){
                if (isset($where['callback'])){
                    $this->exeecuteCallbackInWhere($where['callback'], $sqlArr);
                    //$this->sqlArr[] = $where['callback'];
                    continue;
                }
                if (isset($where['sql'])){
                    $this->conditions[] = $where['sql'];
                    $this->params = array_merge($this->params, $where['params']);
                    continue;
                }
                if ($where['operator'] == 'IN'){
                    $this->whereIn($where['field'], $where['value'], $this->conditions);                    
                    continue;
                }
                $this->conditions[] = $where['field'] . ' ' . $where['operator'] . ' ?';
                $this->params[] = $where['value'];
            }
            $where = implode(' AND ', $this->conditions);
            
            $sqlArr[] = $where;
        }
    }

    private function exeecuteCallbackInWhere($callback, &$conditions){
        $condition = [
            'group' => [],
            'operator' => 'AND',
            'condition'=> '',
            'params' => []
        ];
        

        $queryBuilder = new QueryBuilder();
        $callback($queryBuilder);
        $sqlBuilder = $queryBuilder->getQuery();
        $sqlBuilder->buildConditions($condition['group']);
        $conditions[] = $condition;
    }

    private function whereIn($field, $values, &$conditions): void{
        foreach($values as $value){
            $this->sqlValues[] = '?';
            $this->params[] = $value;
        }
        $conditions[] = $field . ' IN (' . implode(', ', $this->sqlValues) . ')';
    }
    
    public function getParams(): array{
        return $this->params;
    }

}