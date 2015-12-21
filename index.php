<?php

require_once 'ORMish.php';
require_once 'Stream.php';
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

$configs = new Configurations();
$article = $configs->spawn();
$article->setMac('7977955873');

//$article = $configs->spawn();
$article->setTimestamps('0', '1450379565');

//$article->setMac('DDKD');

$results = $configs->findBetween($article);

echo '<pre>';
print_r($results);
