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

interface RouteConditionHandlerInterface
{
    public function getHost(): ?string;
    public function getName(): ?string;
    public function getPort(): ?int;
    public function getScheme(): ?string;
    public function setHost(string $host): RouteConditionHandlerInterface;
    public function setName(string $name): RouteConditionHandlerInterface;
    public function setPort(int $port): RouteConditionHandlerInterface;
    public function setScheme(string $scheme): RouteConditionHandlerInterface;
}
