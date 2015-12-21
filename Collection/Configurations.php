<?php
namespace Datto\ORM\Collection;

use Datto\ORM\Article\Configuration;
use Datto\ORM\Stream;
use Datto\ORM\Query;
use Datto\ORM\Collection\Collection;

class Configurations extends Collection
{
    protected $collection;
    protected $table;

    public function __construct()
    {
        $this->collection = array();

        Query::$table = 'configuration';
    }

    public function spawn()
    {
        $article = new Configuration();

        array_push($this->collection, $article);

        return $article;
    }

    public function find($article)
    {
        $stream = new Stream($article);

        Query::buildSelect('where');
        Query::run();

        return $this->populate(Query::$results);
    }

    public function save($article)
    {
        $stream = new Stream($article);

        Query::buildInsert('values');
        Query::run();
    }

    public function findBetween($article)
    {
        $stream = new Stream($article);

        Query::buildSelect('where.whereBetween', array(
            'whereBetween' => 'timestamp'
        ));

        echo Query::query();

        Query::run();

        return $this->populate(Query::$results);
    }

    public function findLatest($article)
    {
        $stream = new Stream($article);

        Query::buildSelect('where.latest', array(
            'latest' => 'timestamp'
        ));

        Query::run();

        return $this->populate(Query::$results);
    }
}
