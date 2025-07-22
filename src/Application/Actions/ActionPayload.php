<?php

declare(strict_types=1);

namespace App\Application\Actions;

use JsonSerializable;

class ActionPayload implements JsonSerializable
{
    private int $statusCode;

    /**
     * @var array<string|int, mixed>|object|null
     */
    private $data;

    private ?ActionError $error;

    /**
     * @param array<string|int, mixed>|object|null $data
     */
    public function __construct(
        int $statusCode = 200,
        $data = null,
        ?ActionError $error = null
    ) {
        $this->statusCode = $statusCode;
        $this->data = $data;
        $this->error = $error;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array<string|int, mixed>|null|object
     */
    public function getData()
    {
        return $this->data;
    }

    public function getError(): ?ActionError
    {
        return $this->error;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $payload = [];

        if ($this->data !== null) {
            $payload = $this->data instanceof JsonSerializable ? $this->data->jsonSerialize() : $this->data;
        } elseif ($this->error !== null) {
            $payload['status'] = $this->statusCode;
            $payload['error'] = $this->error;
        }

        return $payload;
    }
}
