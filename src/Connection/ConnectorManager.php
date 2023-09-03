<?php

namespace Jonathan13779\Database\Connection;

use Jonathan13779\Database\Connection\ConnectorDTO;
use Jonathan13779\Database\Connection\ConnectorProvider;
use PDO;

class ConnectorManager{
    private static $connectors = [];
    private static ?string $defaultConnector = null;

    public static function addConnector(ConnectorDTO $connector){
        self::$connectors[$connector->name] = $connector;
        if(self::$defaultConnector === null){
            self::$defaultConnector = $connector->name;
        }
    }

    public static function getConnector(?string $name = null): ConnectorDTO{

        if($name === null){
            $name = self::$defaultConnector;
        }
     
        return self::$connectors[$name];
    }

    public static function setDefaultConnector(string $name){
        self::$defaultConnector = $name;
    }

    public static function getDefaultConnector(): ConnectorDTO{
        return self::getConnector(self::$defaultConnector);
    }

    public static function connect(?string $name = null): PDO{
        $connector = self::getConnector($name);
        $dsn = "{$connector->driver}:host={$connector->host};port={$connector->port};dbname={$connector->database}";
        $pdo = new PDO($dsn, $connector->username, $connector->password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }


    public static function register(ConnectorProvider $connectorProvider){
        $connectorProvider::register();
    }


}