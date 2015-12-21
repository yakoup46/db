<?php
namespace Datto\ORM;

abstract class Query extends QueryBuilder {

    public static $table;
    public static $connection;
    public static $properties;
    public static $results;
    public static $limit;
    public static $hasWhere = false;
    public static $sql = array();

    public static function buildSelect($extra = false, $fills = false)
    {
        array_push(self::$sql, 'SELECT * FROM ' . self::$table);

        if ($extra !== false) {
            $parts = explode('.', $extra);

            foreach ($parts as $part) {
                if (isset($fills[$part])) {

                    if (self::$hasWhere) {
                        array_push(self::$sql, self::also());
                    }

                    array_push(self::$sql, self::$part($fills[$part]));
                } else {
                    array_push(self::$sql, self::$part());
                }
            }
        }
    } 

    public static function buildInsert($extra = false, $fills = false)
    {
        array_push(self::$sql, 'INSERT INTO ' . self::$table);

        if ($extra !== false) {
            $parts = explode('.', $extra);

            foreach ($parts as $part) {
                array_push(self::$sql, self::$part());
            }
        }
    }

    public static function latest($field)
    {
        return $field . ' = (SELECT MAX(' . $field . ') FROM ' . self::$table . ' ' . self::where() .')';
    }

    public static function also()
    {
        return ' AND ';
    }

    public static function values()
    {
        return '(' . self::getColumns() . ') VALUES (:' . self::getParams() . ')';
    }

    protected static function getColumns()
    {
        return implode(',', array_keys(self::$properties));
    }

    public static function where()
    {
        $sets = self::getSets();

        if (!empty($sets)) {
            self::$hasWhere = true;
        }

        return 'WHERE ' . $sets;  
    }

    public static function limit($limit) {
        self::$limit = ' LIMIT ' . $limit;
    }

    public static function whereBetween($between)
    {
        return $between . ' BETWEEN ' . self::param($between, 1) . self::also() . self::param($between, 2);
    }

    public static function query()
    {
        return implode(' ', self::$sql);
    }

    public static function run()
    {
        $sql = self::query();
        self::$sql = array();

        if (isset(self::$limit)) {
            $sql .= self::$limit;
        }

        $stmt = self::$connection->prepare($sql);

        self::bind($stmt)->execute();

        self::$results = $stmt;
    }

    private static function bind(\PDOStatement $stmt)
    {
        if (isset(self::$properties)) {
            foreach (self::$properties as $k => $v) {
                if (!is_array($v)) {
                    $stmt->bindValue(":$k", $v);
                } else {
                    $stmt->bindValue(":$k"."1", $v[0]);
                    $stmt->bindValue(":$k"."2", $v[1]);
                }
            }
        }

        return $stmt;
    }

    private static function getSets($seperator = ',')
    {
        $sets = array();

        foreach (self::$properties as $k => $v) {
            if (!is_array($v)) {
                array_push($sets, $k . ' = :' . $k);
            }
        }

        return implode($seperator, $sets);
    }
}
