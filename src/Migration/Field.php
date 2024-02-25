<?php

namespace Jonathan13779\Database\Migration;

class Field
{
    public ?int $length = null;
    public bool $nullable = false;
    public bool $primary = false;

    public function __construct(
            public string $name, 
            public string $type
        )
    {
    }

    public function nullable(): Field
    {
        $this->nullable = true;
        return $this;
    }

    public function length(int $length): Field
    {
        $this->length = $length;
        return $this;
    }

    public function primary(): Field
    {
        $this->primary = true;
        return $this;
    }

    public function toSql(): string
    {
        $sql = "$this->name $this->type";
        if($this->length){
            $sql .= "($this->length)";
        }
        /*if($this->primary){
            $sql .= " PRIMARY KEY";
        }*/
        if($this->nullable){
            $sql .= " NULL";
        }else{
            $sql .= " NOT NULL";
        }
        return $sql;
    }

    public function __toString(): string
    {
        return $this->toSql();
    }

}