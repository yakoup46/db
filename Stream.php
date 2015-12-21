<?php
namespace Datto\ORM;

class Stream {

    public function __construct($article)
    {
        $this->article = $article;

        if ($this->article !== false) {
            $this->buildProperties();
        }
    }

    public function buildProperties()
    {
        $fields = array();

        $clone = clone $this->article;

        $reflect = new \ReflectionClass($clone);
        $props = $reflect->getProperties();

        foreach ($props as $prop) {
            $getter = 'get' . ucfirst($prop->getName());
            $set = $clone->$getter();

            if (isset($set)) {
                $fields[$prop->getName()] = $set;
            }
        }

        Query::$properties = $fields;

        if (method_exists($clone, 'onSave')) {
            $clone->onSave();
        }
    }
}