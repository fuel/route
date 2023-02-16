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

use FastRoute\{DataGenerator, RouteCollector, RouteParser};
use InvalidArgumentException;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{RequestHandlerInterface, MiddlewareInterface};
use Fuel\Container\{ContainerAwareInterface, ContainerAwareTrait};

class Router implements
    MiddlewareInterface,
    ContainerAwareInterface,
    RouteCollectionInterface,
    RouteConditionHandlerInterface
{
    use ContainerAwareTrait;
    use RouteCollectionTrait;
    use RouteConditionHandlerTrait;

    protected const IDENTIFIER_SEPARATOR = "\t";

    /**
     * @var RouteGroup[]
     */
    protected $groups = [];

    /**
     * @var Route[]
     */
    protected $namedRoutes = [];

    /**
     * @var array
     */
    protected $patternMatchers = [
        '/{(.+?):number}/'        => '{$1:[0-9]+}',
        '/{(.+?):word}/'          => '{$1:[a-zA-Z]+}',
        '/{(.+?):alphanum_dash}/' => '{$1:[a-zA-Z0-9-_]+}',
        '/{(.+?):slug}/'          => '{$1:[a-z0-9-]+}',
        '/{(.+?):uuid}/'          => '{$1:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}+}'
    ];

    /**
     * @var RouteCollector
     */
    protected $routeCollector;

    /**
     * @var Route[]
     */
    protected $routes = [];

    /**
     * @var bool
     */
    protected $routesPrepared = false;

    /**
     * @var array
     */
    protected $routesData = [];

    /**
     */
    public function __construct(?RouteCollector $routeCollector = null)
    {
        $this->routeCollector = $routeCollector ?? new RouteCollector(
            new RouteParser\Std(),
            new DataGenerator\GroupCountBased()
        );
    }

    /**
     */
    public function addPatternMatcher(string $alias, string $regex): self
    {
        $pattern = '/{(.+?):' . $alias . '}/';
        $regex = '{$1:' . $regex . '}';
        $this->patternMatchers[$pattern] = $regex;
        return $this;
    }

    /**
     */
    public function group(string $prefix, callable $group): RouteGroup
    {
        $group = new RouteGroup($prefix, $group, $this);
        $this->groups[] = $group;
        return $group;
    }

    /**
     */
    public function getNamedRoute(string $name): Route
    {
        if ( ! $this->routesPrepared)
        {
            $this->collectGroupRoutes();
        }

        $this->buildNameIndex();

        if (isset($this->namedRoutes[$name]))
        {
            return $this->namedRoutes[$name];
        }

        throw new InvalidArgumentException(sprintf('No route of the name (%s) exists', $name));
    }

    /**
     */
    public function map(string $method, string $path, $handler): Route
    {
        $path  = sprintf('/%s', ltrim($path, '/'));
        $route = new Route($method, $path, $handler);

        $this->routes[] = $route;

        return $route;
    }

    /**
     * Middleware interface
     */
    public function process(ServerRequestInterface $request, ?RequestHandlerInterface $handler): ResponseInterface
    {
        // prepare the added routes if not already done
        if (false === $this->routesPrepared)
        {
            $this->prepareRoutes($request);
        }

        /** @var Dispatcher $dispatcher */
        $dispatcher = (new Dispatcher($this->routesData));

        // check if we can get a match on a route
        $route = $dispatcher->dispatchRequest($request);

        if ( ! $route)
        {
            // if not, pass the request on to the next handler
            return $handler->handle($request);
        }

        // route match! get the controller from it
        $controller = $route->getCallable($this->container);

        // call it, return the response
        $response = $controller($request, $route->getVars());
    }

    /**
     */
    public function prepareRoutes(ServerRequestInterface $request): void
    {
        $this->processGroups($request);
        $this->buildNameIndex();

        $routes = array_merge(array_values($this->routes), array_values($this->namedRoutes));

        $options = [];

        /** @var Route $route */
        foreach ($routes as $route)
        {
            // this allows for the same route to be mapped across different routes/hosts etc
            if (false === $this->isExtraConditionMatch($route, $request))
            {
                continue;
            }

            $this->routeCollector->addRoute($route->getMethod(), $this->parseRoutePath($route->getPath()), $route);

            // need a messy but useful identifier to determine what methods to respond with on OPTIONS
            $identifier = $route->getScheme() . static::IDENTIFIER_SEPARATOR . $route->getHost()
                . static::IDENTIFIER_SEPARATOR . $route->getPort() . static::IDENTIFIER_SEPARATOR . $route->getPath();

            // if there is a defined OPTIONS route, do not generate one
            if ('OPTIONS' === $route->getMethod())
            {
                unset($options[$identifier]);
                continue;
            }

            if ( ! isset($options[$identifier]))
            {
                $options[$identifier] = [];
            }

            $options[$identifier][] = $route->getMethod();
        }

        $this->buildOptionsRoutes($options);

        $this->routesPrepared = true;
        $this->routesData = $this->routeCollector->getData();
    }

    /**
     */
    protected function buildNameIndex(): void
    {
        foreach ($this->routes as $key => $route)
        {
            if ($route->getName() !== null)
            {
                unset($this->routes[$key]);
                $this->namedRoutes[$route->getName()] = $route;
            }
        }
    }

    /**
     */
    protected function buildOptionsRoutes(array $options): void
    {
        $getOptionsCallable = function ($methods): ResponseInterface {
            $options  = implode(', ', $methods);
            // @TODO Is this really needed?
            $response = new \Fuel\Http\Response\Response;
            $response = $response->withHeader('allow', $options);
            return $response->withHeader('access-control-allow-methods', $options);
        };

        foreach ($options as $identifier => $methods)
        {
            [$scheme, $host, $port, $path] = explode(static::IDENTIFIER_SEPARATOR, $identifier);
            $route = new Route('OPTIONS', $path, $getOptionsCallable($methods));

            if (!empty($scheme))
            {
                $route->setScheme($scheme);
            }

            if (!empty($host))
            {
                $route->setHost($host);
            }

            if (!empty($port))
            {
                $route->setPort((int) $port);
            }

            $this->routeCollector->addRoute($route->getMethod(), $this->parseRoutePath($route->getPath()), $route);
        }
    }

    /**
     */
    protected function collectGroupRoutes(): void
    {
        foreach ($this->groups as $group)
        {
            $group();
        }
    }

    /**
     */
    protected function processGroups(ServerRequestInterface $request): void
    {
        $activePath = $request->getUri()->getPath();

        foreach ($this->groups as $key => $group)
        {
            unset($this->groups[$key]);
            $group();
        }
    }

    /**
     */
    protected function parseRoutePath(string $path): string
    {
        return preg_replace(array_keys($this->patternMatchers), array_values($this->patternMatchers), $path);
    }
}
