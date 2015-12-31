<?php 
namespace Datto\ORM;

use Datto\ORM\Query;

class ORMish {

    /**
     * @var ORMish $instance singleton
     */
    protected static $instance;
    
    /**
     * @var \PDO $connection PDO connection
     */
    public static $connection;

    /**
     * New instance
     *
     * @param \PDO $pdo Instance of PDO
     * @return ORMish
     */
    public static function init(\PDO $pdo)
    {
        if (!isset(self::$instance)) {
            self::$instance = new ORMish($pdo);
        }

        return self::$instance;
    }

    /**
     * Set our connection
     *
     * @param \PDO $connection Instance of PDO
     */
    protected function __construct(\PDO $connection)
    {
       self::$connection = $connection;
    }
}
