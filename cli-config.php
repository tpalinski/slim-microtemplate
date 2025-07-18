<?php

use DI\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Dotenv\Dotenv;

require __DIR__ .'/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
/** @var Container $container **/
$container = require __DIR__ . '/app/container.php';
$em = $container->get(EntityManager::class);

ConsoleRunner::run(new SingleManagerProvider($em));
