<?php

namespace Jonathan13779\Database\Migration;

use Jonathan13779\Database\Connection\ConnectorManager;
use Jonathan13779\Database\Migration\Table;

class Schema
{

    public static function reset()
    {
        $pdo = ConnectorManager::connect();
        $pdo->exec('DROP SCHEMA public CASCADE; CREATE SCHEMA public;');
    }

    public static function createTable(string $tableName, $callback)
    {
        $newTable = new Table($tableName);
        $callback($newTable);
        $sql = $newTable->toSql();
        $pdo = ConnectorManager::connect();
        $pdo->exec($sql);

    }
}