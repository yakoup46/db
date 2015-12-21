<?php 
namespace Datto\ORM;

use Datto\ORM\Query;

class ORMish {

    protected static $instance;
    
    public static $connection;

    public static function init(\PDO $pdo)
    {
        if (!isset(self::$instance)) {
            self::$instance = new ORMish($pdo);
        }

        return self::$instance;
    }

    protected function __construct(\PDO $connection)
    {
        Query::$connection = $connection;
    }
}
