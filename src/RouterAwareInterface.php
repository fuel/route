<?php declare(strict_types=1);

/**
 * The Fuel PHP Framework is a fast, simple and flexible development framework
 *
 * @package    fuel
 * @version    2.0.0
 * @author     FlexCoders Ltd, Fuel The PHP Framework Team
 * @license    MIT License
 * @copyright  2023 FlexCoders Ltd, The Fuel PHP Framework Team
 * @link       https://fuelphp.org
 */

namespace Fuel\Route;

/**
 * @since 2.0
 */
interface RouterAwareInterface
{
    /**
     * Returns the Router
     *
     * @return Router
     */
    public function getRouter(): Router;

    /**
     * Sets the Router
     *
     * @param Router $router
     */
    public function setRouter(Router $router): void;
}
