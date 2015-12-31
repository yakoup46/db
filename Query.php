<?php
namespace Datto\ORM;

use Datto\ORM\QueryBuilder;

class Query extends QueryBuilder {

    protected $sql = array();
    protected $limit;

    public function buildSelect($extra = false, $fills = false)
    {
        array_push($this->sql, 'SELECT * FROM ' . $this->table);

        array_push($this->sql, $this->where());

        if ($extra !== false) {
            $parts = explode('.', $extra);

            foreach ($parts as $part) {
                if (isset($fills[$part])) {

                    if ($this->hasWhere) {
                        array_push($this->sql, $this->also());
                    }

                    array_push($this->sql, $this->$part($fills[$part]));
                } else {
                    array_push($this->sql, $this->$part());
                }
            }
        }

        return $this;
    }

    public function buildInsert($extra = false, $fills = false)
    {
        array_push($this->sql, 'INSERT INTO ' . $this->table);

        $this->where();

        if ($extra !== false) {
            $parts = explode('.', $extra);

            foreach ($parts as $part) {
                array_push($this->sql, $this->$part());
            }
        }

        return $this;
    }

    // Custom Queries

    /**
     * Specify a range result set
     *
     * @param string $between db column
     * @return string
     */

    public function whereBetween($between)
    {
        return $between . ' BETWEEN ' . $this->param($between, 1) . $this->also() . $this->param($between, 2);
    }

    /**
     * Get lastest result by field
     *
     * @param string $field db column
     * @return string
     */
    public function latest($field)
    {
        return $field . ' = (SELECT MAX(' . $field . ') FROM ' . $this->table . ' ' . $this->where() .')';
    }

    // ---
}