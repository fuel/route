<?php declare(strict_types=1);

/**
 * The Fuel PHP Framework is a fast, simple and flexible development framework
 *
 * @package    fuel
 * @version    2.0.0
 * @author     FlexCoders Ltd, Fuel The PHP Framework Team
 * @license    MIT License
 * @copyright  2019-2021 Phil Bennett
 * @copyright  2023 FlexCoders Ltd, The Fuel PHP Framework Team
 * @link       https://fuelphp.org
 */

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
