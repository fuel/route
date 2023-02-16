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

namespace Fuel\Route\Middleware;

use Psr\Http\Server\MiddlewareInterface;

interface MiddlewareAwareInterface
{

    public function getMiddlewareStack(): iterable;
    public function lazyMiddleware(string $middleware): MiddlewareAwareInterface;
    public function lazyMiddlewares(array $middlewares): MiddlewareAwareInterface;
    public function lazyPrependMiddleware(string $middleware): MiddlewareAwareInterface;
    public function middleware(MiddlewareInterface $middleware): MiddlewareAwareInterface;
    public function middlewares(array $middlewares): MiddlewareAwareInterface;
    public function prependMiddleware(MiddlewareInterface $middleware): MiddlewareAwareInterface;
    public function shiftMiddleware(): MiddlewareInterface;
}
