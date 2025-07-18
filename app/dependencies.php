<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get(Settings::LOGGER);
            $logger = new Logger($loggerSettings[Settings::LOGGER_NAME]);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings[Settings::LOGGER_PATH], $loggerSettings[Settings::LOGGER_LEVEL]);
            $logger->pushHandler($handler);

            return $logger;
        },
        SerializerInterface::class => function (ContainerInterface $c) {
            $builder = new SerializerBuilder();
            return $builder->build();
        },
        EntityManager::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class)->get(Settings::DOCTRINE);
            $devMode = $settings[Settings::DOCTRINE_DEV_MODE];
            $paths = $settings[Settings::DOCTRINE_METADATA_DIRS];
            $config = ORMSetup::createAttributeMetadataConfiguration($paths,$devMode);
            $connectionSettings = $settings[Settings::DOCTRINE_CONNECTION];
            $connectionConfig = [
                'driver' => $connectionSettings[Settings::DOCTRINE_DRIVER],
                'host' => $connectionSettings[Settings::DOCTRINE_HOST],
                'port' => $connectionSettings[Settings::DOCTRINE_PORT],
                'user' => $connectionSettings[Settings::DOCTRINE_USER],
                'password' => $connectionSettings[Settings::DOCTRINE_PASSWORD],
                'dbname' => $connectionSettings[Settings::DOCTRINE_DB]

            ];
            $connection = DriverManager::getConnection($connectionConfig, $config);
            return new EntityManager($connection, $config);
        }
    ]);
};
