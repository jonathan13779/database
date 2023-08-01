<?php

namespace Jonathan13779\Database\Connection;

class ConnectorDTO{
    public function __construct(
        public readonly string $name,
        public readonly string $driver = 'pgsql',
        public readonly string $host = 'localhost',
        public readonly string $port = '5432',
        public readonly string $database = 'postgres',
        public readonly string $username = 'postgres',
        public readonly string $password = 'postgres',
        public readonly string $charset = 'utf8',
        public readonly string $collation = 'utf8_unicode_ci',
        public readonly string $prefix = ''
    ){
    }
}