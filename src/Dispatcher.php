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
use Fuel\Http\Message\Uri;
use RuntimeException;

/**
 */
class Dispatcher extends GroupCountBasedDispatcher implements RouteConditionHandlerInterface
{
    use RouteConditionHandlerTrait;

    /**
     */
    public function dispatchRequest(ServerRequestInterface $request): ServerRequestInterface|Route|int
    {
        // resolve the request method and URI
        $method = $request->getMethod();
        $uri    = $request->getUri()->getPath();
        $match  = $this->dispatch($method, $uri);

        // process the result
        switch ($match[0])
        {
            case FastRoute::NOT_FOUND:
                break;

            case FastRoute::METHOD_NOT_ALLOWED:
                break;

            case FastRoute::FOUND:
                // make sure the route is ok
                $route = $this->ensureHandlerIsRoute($match[1], $method, $uri)->setVars($match[2]);

                // check if conditions may reject it
                if ($this->isExtraConditionMatch($route, $request))
                {
                    // is this a forward route?
                    if ($forward = $route->getForwardTo())
                    {
                        // check if we need to replace matched arguments
                        foreach ($match[2] ?: [] as $key => $value)
                        {
                            $forward = str_replace("{".$key."}", $value, $forward);
                        }

                        // create a new request with the forwarded URI
                        return $request->withAttribute('RouteVars', $route->getVars())->withUri(new Uri($forward), true);
                    }

                    // return the found route
                    return $route;
                }

                // conditions not matched? should not happen, but just in case...
                $match[0] = FastRoute::NOT_FOUND;
                break;
        }

        // return the FastRoute status code
        return $match[0];
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
