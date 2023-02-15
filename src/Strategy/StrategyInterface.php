<?php

declare(strict_types=1);

namespace Fuel\Route\Strategy;

use Fuel\Route\Http\Exception\{MethodNotAllowedException, NotFoundException};
use Fuel\Route\Route;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\MiddlewareInterface;

interface StrategyInterface
{
    public function addResponseDecorator(callable $decorator): StrategyInterface;
    public function getMethodNotAllowedDecorator(MethodNotAllowedException $exception): MiddlewareInterface;
    public function getNotFoundDecorator(NotFoundException $exception): MiddlewareInterface;
    public function getThrowableHandler(): MiddlewareInterface;
    public function invokeRouteCallable(Route $route, ServerRequestInterface $request): ResponseInterface;
}
