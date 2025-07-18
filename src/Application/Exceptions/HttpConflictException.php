<?php

declare(strict_types = 1);

namespace App\Application\Exceptions;

use Slim\Exception\HttpException;

class HttpConflictException extends HttpException {
    /**
     * @var int
     */
    protected $code = 409;

    /**
     * @var string
     */
    protected $message = 'Conflict';

    protected string $title = '409 Conflict';
    protected string $description = 'Requested resource could not be created. Most likely it already exists';
}
