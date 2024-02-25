<?php

namespace Jonathan13779\Database\Migration;

class Index{
    public function __construct(
        private string $column,
        private string $type = 'INDEX'
    )
    {
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function unique(){
        $this->type = 'UNIQUE';
    }
}