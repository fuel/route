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

namespace Fuel\Route;

use FastRoute\Dispatcher as FastRoute;
use FastRoute\Dispatcher\GroupCountBased as GroupCountBasedDispatcher;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

/**
 */
class Dispatcher extends GroupCountBasedDispatcher implements RouteConditionHandlerInterface
{
    use RouteConditionHandlerTrait;

    /**
     */
    public function dispatchRequest(ServerRequestInterface $request): Route|bool
    {
        $method = $request->getMethod();
        $uri    = $request->getUri()->getPath();
        $match  = $this->dispatch($method, $uri);

        switch ($match[0]) {
            case FastRoute::NOT_FOUND:
                break;
            case FastRoute::METHOD_NOT_ALLOWED:
                break;
            case FastRoute::FOUND:
                $route = $this->ensureHandlerIsRoute($match[1], $method, $uri)->setVars($match[2]);
                if ($this->isExtraConditionMatch($route, $request))
                {
                    return $route;
                }
                break;
        }

        return false;
    }

    /**
     */
    protected function ensureHandlerIsRoute($matchingHandler, $httpMethod, $uri): Route
    {
        if ($matchingHandler instanceof Route)
        {
            return $matchingHandler;
        }

        return new Route($httpMethod, $uri, $matchingHandler);
    }
}
