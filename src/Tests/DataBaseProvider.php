<?php
namespace Jonathan13779\Database\Tests;

use Jonathan13779\Database\Connection\ConnectorProvider;
use Jonathan13779\Database\Connection\ConnectorDTO;
use Jonathan13779\Database\Connection\ConnectorManager;

class DataBaseProvider extends ConnectorProvider
{
    public static function register()
    {
        $connector = new ConnectorDTO(
            name: 'test',
            host: 'postgres',
            database: 'gamerteca',
            username: 'zataca',
            password: 'zataca'
        );
        ConnectorManager::addConnector($connector);   
    }
}