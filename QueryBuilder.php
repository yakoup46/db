<?php
namespace Datto\ORM;

class QueryBuilder {

    /**
     * @var array $properties Defined properties of a given Article
     */
    protected $properties;

    /**
     * @var string $table SQL table
     */
    protected $table;

    /**
     * boolean $hasWhere Denotes there was a previous where condition
     */
    protected $hasWhere;

    /**
     * Construct
     *
     * @param mixed $article An Article
     * @param boolean $saving Denotes we are doing an INSERT
     */
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

    /**
     * Prepares and executes a query
     */
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

    /**
     * Return string AND
     */
    public static function also()
    {
        return ' AND ';
    }

    /**
     * Creates parameterized string for PDO
     * for properties with more than one value
     *
     * @param string $param Property
     * @param int $count Properties increment
     */
    protected function param($param, $count)
    {
        return implode('', array(':', $param, $count));
    }

    /**
     * Creates a values string for an INSERT
     *
     * @return string
     */
    public function values()
    {
        return '(' . $this->getColumns() . ') VALUES (' . $this->getParams() . ')';
    }

    /**
     * Comma seperated list of columns
     *
     * @return string
     */
    public function getColumns()
    {
        return implode(',', array_keys($this->properties));
    }

    /**
     * Creates parameterized string for PDO
     * for all set properties
     *
     * @return string
     */
    protected function getParams()
    {
        return ':' . implode(',:', array_keys($this->properties));
    }

    /**
     * Puts the SQL parts into a readable SQL statement
     *
     * @return string
     */
    public function query()
    {
        return implode(' ', $this->sql);
    }

    /**
     * Binds our PDO values
     *
     * @param \PDOStatement $stm PDO statement
     * @return \PDOStatement
     */
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

    /**
     * Sets basic where conditions for any set properties
     *
     * @return string
     */
    protected function where()
    {
        $sets = $this->getWhereConditions();

        if (!empty($sets)) {
            $this->hasWhere = true;
        }

        return 'WHERE ' . $sets;  
    }

    /**
     * Get our table from our Article class name
     *
     * @return string
     */
    private function getTable($article)
    {
        return strtolower(substr(strrchr(get_class($article), "\\"), 1));
    }

    /**
     * Creates PDO parameters based on any set properties in our Article
     *
     * @return string
     */
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