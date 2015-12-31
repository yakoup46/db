<?php
namespace Datto\ORM\Collection;

class Collection
{
    protected function populate($stmt)
    {
        $output = array();
        //$results = $stmt->fetchAll(\PDO::FETCH_CLASS);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, "\\Datto\\ORM\\Article\\Configuration");

        // foreach ($results as $result) {
        //     $class = current($this->collection);
        //     $obj = new $class;

        //     foreach ($result as $prop => $val) {
        //         $setter = 'set' . ucfirst($prop);

        //         if (method_exists($obj, $setter)) {
        //             $obj->$setter($val);
        //         }
        //     }

        //     array_push($output, $obj);
        // }

        return $output;
    }
}
