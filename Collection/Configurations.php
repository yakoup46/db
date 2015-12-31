<?php
namespace Datto\ORM\Collection;

use Datto\ORM\Article\Configuration;
use Datto\ORM\Query;
use Datto\ORM\Collection\Collection;

class Configurations extends Collection
{
    public function spawn()
    {
        return new Configuration();
    }

    public function find($article)
    {
        $qb = new Query($article);

        $qb->buildSelect()->run();

        return $this->populate($qb->results);
    }

    public function save($article)
    {
        $qb = new Query($article, true);

        $qb->buildInsert('values')
        ->run();
    }

    public function findBetween($article)
    {
        $qb = new Query($article);

        $qb->buildSelect('whereBetween', array(
            'whereBetween' => 'timestamp'
        ))->run();

        return $this->populate($qb->results);
    }

    public function findLatest($article)
    {
        $qb = new Query($article);

        $qb->buildSelect('latest', array(
            'latest' => 'timestamp'
        ))->run();

        return $this->populate($qb->results);
    }
}
