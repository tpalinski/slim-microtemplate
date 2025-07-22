<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;


if(!function_exists('getEnvWithDefaults')) {
  function getEnvWithDefaults(string $key, mixed $default = null): mixed {
    $filters = [
        'boolean' => FILTER_VALIDATE_BOOLEAN,
        'integer' => FILTER_VALIDATE_INT,
        'double'  => FILTER_VALIDATE_FLOAT,
        'float' => FILTER_VALIDATE_FLOAT,
        'string'  => null,
        'NULL'    => null,
    ];
    $fallbackType = gettype($default);
    if(!isset($_ENV[$key])) {
        return $default;
    }
    $envString = $_ENV[$key];
    if(!isset($filters[$fallbackType]) || $default===null) {
        return $envString;
    }
    // string "NULL" is kinda scuffed in terms of hollistic interpretation, so we parse it on its own
    // tbh i do not really know if there is a standard to handling it, just dont put NULL in the dotenv. why would one do it anyway. if there are deployment bugs, this will probably be the thing that causes them
    if(strtoupper($envString) === 'NULL') {
        return null;
    }
    // Automatically parse dotenv string into desired type based on default value
    $parsedEnv = filter_var($envString, $filters[$fallbackType], FILTER_NULL_ON_FAILURE);
    $res = $parsedEnv === null ? $default : $parsedEnv;
    return $res;
  }
}

if(!function_exists('setupLogger')) {
  function setupLogger(): mixed {
    /** @var array<int> $levelMap */
    $levelMap = [
      'debug' => Logger::DEBUG,
      'info' => Logger::INFO,
      'critical' => Logger::CRITICAL,
    ];
    $loggerLevel = $levelMap[getEnvWithDefaults(Settings::LOGGER_LEVEL, 'info')] ?? Logger::INFO;
    return [
      Settings::LOGGER_LEVEL => $loggerLevel,
      Settings::LOGGER_PATH => isset($_ENV[Settings::DOCKER]) ? 'php://stdout' : __DIR__.'/../'.getEnvWithDefaults(Settings::LOGGER_PATH, 'logs/app.log'),
      Settings::LOGGER_NAME => getEnvWithDefaults(Settings::LOGGER_NAME, 'gatekeeper'),
    ];
  }
}

if(!function_exists('setupDoctrine')) {
  function setupDoctrine(): mixed {
    return [
      Settings::DOCTRINE_DEV_MODE => getEnvWithDefaults(Settings::DOCTRINE_DEV_MODE, false),
      Settings::DOCTRINE_CACHE_DIR => __DIR__.'/../'.getEnvWithDefaults(Settings::DOCTRINE_CACHE_DIR, 'var/cache/doctrine'),
      Settings::DOCTRINE_METADATA_DIRS => [__DIR__.'/../src/Domain/Entity'],
      Settings::DOCTRINE_CONNECTION => [
        Settings::DOCTRINE_DRIVER => 'pdo_pgsql',
        Settings::DOCTRINE_HOST => getEnvWithDefaults(Settings::DOCTRINE_HOST, 'postgres'),
        Settings::DOCTRINE_PORT => getEnvWithDefaults(Settings::DOCTRINE_PORT, 5432),
        Settings::DOCTRINE_USER => getEnvWithDefaults(Settings::DOCTRINE_USER, 'admin'),
        Settings::DOCTRINE_PASSWORD => getEnvWithDefaults(Settings::DOCTRINE_PASSWORD),
        Settings::DOCTRINE_DB => getEnvWithDefaults(Settings::DOCTRINE_DB, 'gates'),
      ]
    ];
  }
}

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                Settings::ERROR_DISPLAY => getEnvWithDefaults(Settings::ERROR_DISPLAY, false),
                Settings::LOG_ERROR => getEnvWithDefaults(Settings::LOG_ERROR, false),
                Settings::LOG_ERROR_DETAILS => getEnvWithDefaults(Settings::LOG_ERROR_DETAILS, false),
                Settings::LOGGER => setupLogger(),
                Settings::DOCTRINE => setupDoctrine(),
            ]);
        }
    ]);
};

