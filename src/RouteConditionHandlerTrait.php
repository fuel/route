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

use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

trait RouteConditionHandlerTrait
{
    /**
     * @var ?string
     */
    protected $host;

    /**
     * @var ?string
     */
    protected $name;

    /**
     * @var ?int
     */
    protected $port;

    /**
     * @var ?string
     */
    protected $scheme;

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    public function setHost(string $host): RouteConditionHandlerInterface
    {
        $this->host = $host;
        return $this->checkAndReturnSelf();
    }

    public function setName(string $name): RouteConditionHandlerInterface
    {
        $this->name = $name;
        return $this->checkAndReturnSelf();
    }

    public function setPort(int $port): RouteConditionHandlerInterface
    {
        $this->port = $port;
        return $this->checkAndReturnSelf();
    }

    public function setScheme(string $scheme): RouteConditionHandlerInterface
    {
        $this->scheme = $scheme;
        return $this->checkAndReturnSelf();
    }

    private function checkAndReturnSelf(): RouteConditionHandlerInterface
    {
        if ($this instanceof RouteConditionHandlerInterface) {
            return $this;
        }

        throw new RuntimeException(sprintf(
            'Trait (%s) must be consumed by an instance of (%s)',
            __TRAIT__,
            RouteConditionHandlerInterface::class
        ));
    }

    protected function isExtraConditionMatch(Route $route, ServerRequestInterface $request): bool
    {
        // check for scheme condition
        $scheme = $route->getScheme();
        if ($scheme !== null && $scheme !== $request->getUri()->getScheme()) {
            return false;
        }

        // check for domain condition
        $host = $route->getHost();
        if ($host !== null && $host !== $request->getUri()->getHost()) {
            return false;
        }

        // check for port condition
        $port = $route->getPort();
        return !($port !== null && $port !== $request->getUri()->getPort());
    }
}
