<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Application\Exceptions\HttpConflictException;
use App\Application\Middleware\DtoExtractor;
use App\Domain\DomainException\DomainConflictException;
use App\Domain\DomainException\DomainRecordNotFoundException;
use JMS\Serializer\SerializerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

abstract class Action
{
    protected LoggerInterface $logger;

    protected SerializerInterface $serializer;

    protected Request $request;

    protected Response $response;

    /**
     * @var array<string, mixed>
     */
    protected array $args;

    public function __construct(LoggerInterface $logger, SerializerInterface $serializer)
    {
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    /**
     * @param array<string, mixed> $args
     * @throws HttpNotFoundException
     * @throws HttpBadRequestException
     * @throws HttpConflictException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        try {
            return $this->action();
        } catch (DomainRecordNotFoundException $e) {
            throw new HttpNotFoundException($this->request, $e->getMessage());
        } catch (DomainConflictException $e) {
            throw new HttpConflictException($this->request, $e->getMessage());
        }
    }

    /**
     * @throws DomainRecordNotFoundException
     * @throws HttpBadRequestException
     * @throws DomainConflictException
     */
    abstract protected function action(): Response;

    /**
     * @return array<string, mixed>|object
     */
    protected function getFormData(): array|object
    {
        return $this->request->getParsedBody();
    }


    /**
     * @return array<string, mixed>|object
     */
    protected function getParsedBody(): array|object {
        $dto = $this->request->getAttribute(DtoExtractor::PARSED_BODY_REQUEST_KEY);
        if(is_null($dto)) {
            return $this->request->getParsedBody();
        }
        return $dto;
    }

    /**
     * @return mixed
     * @throws HttpBadRequestException
     */
    protected function resolveArg(string $name)
    {
        if (!isset($this->args[$name])) {
            throw new HttpBadRequestException($this->request, "Could not resolve argument `{$name}`.");
        }

        return $this->args[$name];
    }

    /**
     * @param array<string|int, mixed>|object|null $data
     */
    protected function respondWithData($data = null, int $statusCode = 200): Response
    {
        $payload = new ActionPayload($statusCode, $data);

        return $this->respond($payload);
    }

    protected function respond(ActionPayload $payload): Response
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT);
        assert($json!==false);
        $this->response->getBody()->write($json);

        return $this->response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus($payload->getStatusCode());
    }
}
