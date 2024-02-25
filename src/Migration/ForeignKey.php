<?php

namespace Jonathan13779\Database\Migration;

class ForeignKey
{
    public string $tableReference;
    public string $columnReference;

    public function __construct(
        public string $columnName
    )
    {
        
    }

    public function references(string $table, string $column): ForeignKey
    {
        $this->tableReference = $table;
        $this->columnReference = $column;

        return $this;
    }
}