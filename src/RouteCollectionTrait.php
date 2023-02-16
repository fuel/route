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

trait RouteCollectionTrait
{
    abstract public function map(string $method, string $path, $handler): Route;

    public function delete(string $path, $handler): Route
    {
        return $this->map('DELETE', $path, $handler);
    }

    public function get(string $path, $handler): Route
    {
        return $this->map('GET', $path, $handler);
    }

    public function head(string $path, $handler): Route
    {
        return $this->map('HEAD', $path, $handler);
    }

    public function options(string $path, $handler): Route
    {
        return $this->map('OPTIONS', $path, $handler);
    }

    public function patch(string $path, $handler): Route
    {
        return $this->map('PATCH', $path, $handler);
    }

    public function post(string $path, $handler): Route
    {
        return $this->map('POST', $path, $handler);
    }

    public function put(string $path, $handler): Route
    {
        return $this->map('PUT', $path, $handler);
    }
}
