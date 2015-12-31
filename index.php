<?php

require_once 'ORMish.php';
require_once 'QueryBuilder.php';
require_once 'Query.php';
require_once 'Article/Configuration.php';
require_once 'Collection/Collection.php';
require_once 'Collection/Configurations.php';

use Datto\ORM\Collection\Configurations;
use Datto\ORM\Query;
use Datto\ORM\ORMish;

$pdo = new \PDO('mysql:host=localhost;dbname=dna', 'root');
ORMish::init($pdo);

$configs = new \Datto\ORM\Collection\Configurations();
$article = $configs->spawn();

$article->setMac('7977955873');

//print_r($article);

$results = $configs->save($article);

$results = $configs->findLatest($article);

echo '<pre>';
print_r($results);
