<?php
namespace Datto\ORM;

class QueryBuilder {

    protected $properties;
    protected $table;
    protected $hasWhere;

    public function __construct($article, $saving = false)
    {
        $fields = array();

        $this->clone = clone $article;
        $this->table = $this->getTable($article);

        if (method_exists($this->clone, 'onSave') && $saving) {
            $this->clone->onSave();
        }

        $reflect = new \ReflectionClass($this->clone);
        $props = $reflect->getProperties();

        foreach ($props as $prop) {
            $getter = 'get' . ucfirst($prop->getName());
            $set = $this->clone->$getter();

            if (isset($set)) {
                $fields[$prop->getName()] = $set;
            }
        }

        $this->properties = $fields;
    }

    public function run()
    {
        $sql = $this->query();

        if (isset($this->limit)) {
            $sql .= $this->limit;
        }

        $stmt = ORMish::$connection->prepare($sql);

        $this->bind($stmt)->execute();

        $this->results = $stmt;
    }

    public static function also()
    {
        return ' AND ';
    }

    protected function param($param, $count)
    {
        return implode('', array(':', $param, $count));
    }

    public function values()
    {
        return '(' . $this->getColumns() . ') VALUES (' . $this->getParams() . ')';
    }

    public function getColumns()
    {
        return implode(',', array_keys($this->properties));
    }

    protected function getParams()
    {
        return ':' . implode(',:', array_keys($this->properties));
    }

    public function query()
    {
        return implode(' ', $this->sql);
    }

    private function bind(\PDOStatement $stmt)
    {
        if (isset($this->properties)) {
            foreach ($this->properties as $k => $v) {
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

    protected function where()
    {
        $sets = $this->getWhereConditions();

        print_r($sets);

        if (!empty($sets)) {
            $this->hasWhere = true;
        }

        return 'WHERE ' . $sets;  
    }

    private function getTable($article)
    {
        return strtolower(substr(strrchr(get_class($article), "\\"), 1));
    }

    private function getWhereConditions($seperator = ',')
    {
        $sets = array();

        foreach ($this->properties as $k => $v) {
            if (!is_array($v)) {
                array_push($sets, $k . ' = :' . $k);
            }
        }

        return implode($seperator, $sets);
    }
}