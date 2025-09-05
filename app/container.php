<?php

use DI\ContainerBuilder;

require __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();

// By now, env is loaded. See index.php
if (isset($_ENV['ENVIRONMENT']) && $_ENV['ENVIRONMENT'] === 'production') {
	$containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
}

$settings = require __DIR__ . '/settings.php';
$settings($containerBuilder);

$dependencies = require __DIR__ . '/dependencies.php';
$dependencies($containerBuilder);

$services = require __DIR__ . '/services.php';
$services($containerBuilder);

$container = $containerBuilder->build();

return $container;
