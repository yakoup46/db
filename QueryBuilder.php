<?php
namespace Datto\ORM;

abstract class QueryBuilder {

    protected static function param($param, $count)
    {
        return implode('', array(':',$param,$count));
    }

    protected static function getParams()
    {
        return ':' . implode(',:', array_keys(self::$properties));
    }

}