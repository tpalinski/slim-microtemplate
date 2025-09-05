<?php

declare(strict_types=1);

use Dotenv\Dotenv;

return function () {
  $dotenv = Dotenv::createImmutable(__DIR__.'/..');
  $dotenv->load();
};
