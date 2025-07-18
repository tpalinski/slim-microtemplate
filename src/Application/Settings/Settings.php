<?php

declare(strict_types=1);

namespace App\Application\Settings;

// There would be a "clever" way of doing it with reflection. However, the less magic, the better
class Settings implements SettingsInterface
{
    public const ERROR_DISPLAY = "ERROR_DISPLAY";
    public const LOG_ERROR = "LOG_ERROR";
    public const LOG_ERROR_DETAILS = "LOG_ERROR_DETAILS";
    public const DOCKER = "DOCKER";
    //Logger stuff
    public const LOGGER = 'LOGGER';
    public const LOGGER_LEVEL = 'LOGGER_LEVEL';
    public const LOGGER_PATH = 'LOGGER_PATH';
    public const LOGGER_NAME = "LOGGER_NAME";
    //Doctrine stuff
    public const DOCTRINE = 'DOCTRINE';
    public const DOCTRINE_DEV_MODE = 'DOCTRINE_DEV_MODE';
    public const DOCTRINE_CACHE_DIR = 'DOCTRINE_CACHE_DIR';
    public const DOCTRINE_METADATA_DIRS = 'DOCTRINE_METADATA_DIRS';
    public const DOCTRINE_CONNECTION = 'CONNECTION';
    public const DOCTRINE_DRIVER = "DOCTRINE_DRIVER";
    public const DOCTRINE_HOST = 'DOCTRINE_HOST';
    public const DOCTRINE_PORT = 'DOCTRINE_PORT';
    public const DOCTRINE_USER = 'DOCTRINE_USER';
    public const DOCTRINE_PASSWORD = 'DOCTRINE_PASSWORD';
    public const DOCTRINE_DB = 'DOCTRINE_DB';

    private array $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return mixed
     */
    public function get(string $key = '')
    {
        return (empty($key)) ? $this->settings : $this->settings[$key];
    }
}
