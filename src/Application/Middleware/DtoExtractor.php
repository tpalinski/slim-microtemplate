<?php

declare(strict_types = 1);

namespace App\Application\Middleware;

use Exception;
use JMS\Serializer\SerializerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpBadRequestException;

// This is a utility middleware to be used with individual routes in `routes.php`, which parses the request into given DTO class using JMSSerializer
class DtoExtractor implements MiddlewareInterface {

  public const PARSED_BODY_REQUEST_KEY = 'parsedBody';

  public function __construct(
	  private SerializerInterface $serializer,
	  private string $parsedClass,
  )
  {}

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
  {
    $rawBody = (string) $request->getBody();
    $newRequest = null;
    try {
      $parsed = $this->serializer->deserialize($rawBody, $this->parsedClass, 'json');
      $newRequest = $request->withAttribute(DtoExtractor::PARSED_BODY_REQUEST_KEY, $parsed);
    } catch (Exception $e) {
      throw new HttpBadRequestException($request, "Invalid request body");
    }
    return $handler->handle($newRequest);
  }
}
