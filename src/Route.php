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

use Psr\Container\ContainerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use RuntimeException;

class Route implements RouteConditionHandlerInterface
{
    use RouteConditionHandlerTrait;

    /**
     * @var callable|string
     */
    protected $handler;

    /**
     * @var RouteGroup
     */
    protected $group;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $vars = [];

    /**
     */
    public function __construct(string $method, string $path, mixed $handler)
    {
        $this->method  = $method;
        $this->path    = $path;
        $this->handler = $handler;
    }

    /**
     */
    public function getCallable(?ContainerInterface $container = null): callable
    {
        $callable = $this->handler;

        if (is_string($callable) && strpos($callable, '::') !== false)
        {
            $callable = explode('::', $callable);
        }

        if (is_array($callable) && isset($callable[0]) && is_object($callable[0]))
        {
            $callable = [$callable[0], $callable[1]];
        }

        if (is_array($callable) && isset($callable[0]) && is_string($callable[0]))
        {
            $callable = [$this->resolve($callable[0], $container), $callable[1]];
        }

        if (is_string($callable))
        {
            $callable = $this->resolve($callable, $container);
        }

        if ( ! is_callable($callable))
        {
            throw new RuntimeException('Could not resolve a callable for this route');
        }

        return $callable;
    }

    /**
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     */
    public function getParentGroup(): ?RouteGroup
    {
        return $this->group;
    }

    /**
     */
    public function getPath(?array $replacements = null): string
    {
        if (null === $replacements)
        {
            return $this->path;
        }

        $hasReplacementRegex = '/{(' . implode('|', array_keys($replacements)) . ')(:.*)?}/';

        preg_match_all('/\[(.*?)?{(?<keys>.*?)}/', $this->path, $matches);

        $isOptionalRegex = '/(.*)?{('
            . implode('|', $matches['keys'])
            . ')(:.*)?}(.*)?/'
        ;

        $isPartiallyOptionalRegex = '/^([^\[\]{}]+)?\[((?:.*)?{(?:'
            . implode('|', $matches['keys'])
            . ')(?::.*)?}(?:.*)?)\]?([^\[\]{}]+)?(?:[\[\]]+)?$/'
        ;

        $toReplace = [];

        foreach ($replacements as $wildcard => $actual)
        {
            $toReplace['/{' . preg_quote($wildcard, '/') . '(:.*)?}/'] = $actual;
        }

        $segments = [];

        foreach (array_filter(explode('/', $this->path)) as $segment)
        {
            // segment is partially optional with a wildcard, strip it if no match, tidy up if match
            if (preg_match($isPartiallyOptionalRegex, $segment))
            {
                $segment = preg_match($hasReplacementRegex, $segment)
                    ? preg_replace($isPartiallyOptionalRegex, '$1$2$3', $segment)
                    : preg_replace($isPartiallyOptionalRegex, '$1', $segment)
                ;
            }

            // segment either isn't a wildcard or there is a replacement
            if ( ! preg_match('/{(.*?)}/', $segment) || preg_match($hasReplacementRegex, $segment))
            {
                $segments[] = preg_replace(['/\[$/', '/\]+$/'], '', $segment);
                continue;
            }

            // segment is a required wildcard, no replacement, still gets added
            if ( ! preg_match($isOptionalRegex, $segment))
            {
                $segments[] = preg_replace(['/\[$/', '/\]+$/'], '', $segment);
                continue;
            }

            // segment is completely optional with no replacement, strip it and break
            if (preg_match($isOptionalRegex, $segment) and ! preg_match($hasReplacementRegex, $segment))
            {
                break;
            }
        }

        return preg_replace(array_keys($toReplace), array_values($toReplace), '/' . implode('/', $segments));
    }

    /**
     */
    public function getVars(): array
    {
        return $this->vars;
    }

    /**
     */
    public function setParentGroup(RouteGroup $group): self
    {
        $this->group = $group;
        $prefix      = $this->group->getPrefix();
        $path        = $this->getPath();

        if (strcmp($prefix, substr($path, 0, strlen($prefix))) !== 0)
        {
            $path = $prefix . $path;
            $this->path = $path;
        }

        return $this;
    }

    /**
     */
    public function setVars(array $vars): self
    {
        $this->vars = $vars;
        return $this;
    }

    /**
     */
    protected function resolve(string $class, ?ContainerInterface $container = null)
    {
        if ($container instanceof ContainerInterface && $container->has($class))
        {
            return $container->get($class);
        }

        if (class_exists($class))
        {
            return new $class();
        }

        return $class;
    }
}
