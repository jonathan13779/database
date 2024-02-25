<?php

namespace Jonathan13779\Database\Migration;

class Table{
    private array $columns = [];
    private array $foreignKeys = [];
    private array $indexes = [];

    public function __construct(
        private string $name
    )
    {
    }


    public function uuid(string $name): Field
    {
        $this->columns[$name] = new Field($name, 'UUID');
        return $this->columns[$name];
    }

    public function string(string $name, $length = 255): Field
    {
        $field = new Field($name, 'VARCHAR');
        $field->length = $length;
        $this->columns[$name] = $field;
        return $this->columns[$name];
    }

    public function integer(string $name): Field
    {
        $this->columns[$name] = new Field($name, 'INTEGER');
        return $this->columns[$name];
    }

    public function text(string $name): Field
    {
        $this->columns[$name] = new Field($name, 'TEXT');
        return $this->columns[$name];
    }

    public function timestamp(string $name): Field
    {
        $this->columns[$name] = new Field($name, 'TIMESTAMP');
        return $this->columns[$name];
    }

    public function boolean(string $name): Field
    {
        $this->columns[$name] = new Field($name, 'BOOLEAN');
        return $this->columns[$name];
    }

    public function jsonb(string $name): Field
    {
        $this->columns[$name] = new Field($name, 'JSONB');
        return $this->columns[$name];
    }

    public function getColumns(){
        return $this->columns;
    }


    public function foreignKey(string $columnName): ForeignKey
    {
        $foreignKey = new ForeignKey($columnName);
        $this->foreignKeys[$columnName] = $foreignKey;
        return $foreignKey;
    }

    public function index(string $columnName): Index
    {
        $index = new Index($columnName);
        $this->indexes[$columnName] = $index;
        return $index;
    }

 
    public function toSql(): string{
        $columns = [];
        foreach ($this->columns as $column) {
            $columns[] = $column->toSql();
        }
        $columns = implode(", ", $columns);
        $sql = "CREATE TABLE $this->name ($columns";
       
        $primaryKeys = $this->buildPrimaryKey();
        if(count($primaryKeys) > 0){
            $sql .= ", PRIMARY KEY (".implode(", ", $primaryKeys).")";
        }

        $foreignKeys = $this->buildForeignKeys();
        if(count($foreignKeys) > 0){
            $sql .= ", ".implode(", ", $foreignKeys);
        }


        $sql .= ");";

        $indexes = $this->buildIndexes();
        if(count($indexes) > 0){
            $sql .= implode(", ", $indexes);
        }

        return $sql;
    }

    public function buildPrimaryKey(){
        $primaryKeys = [];
        foreach ($this->columns as $column) {
            if($column->primary){
                $primaryKeys[] = $column->name;
            }
        }
        return $primaryKeys;
    }

    public function buildForeignKeys(){
        $foreignKeys = [];
        foreach ($this->foreignKeys as $foreignKey) {
            $foreignKeys[] = "FOREIGN KEY ($foreignKey->columnName) REFERENCES $foreignKey->tableReference($foreignKey->columnReference)";
        }
        return $foreignKeys;
    }

    public function buildIndexes(){
        $indexes = [];
        foreach ($this->indexes as $index) {
            $table = $this->name;
            $indexes[] = "CREATE INDEX idx_".$index->getColumn()." ON $table (".$index->getColumn().");";
        }
        return $indexes;
    }

}