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

use Psr\Http\Message\ResponseInterface;

abstract class AbstractStrategy implements StrategyInterface
{
    /**
     * @var array
     */
    protected $responseDecorators = [];

    public function addResponseDecorator(callable $decorator): StrategyInterface
    {
        $this->responseDecorators[] = $decorator;
        return $this;
    }

    protected function decorateResponse(ResponseInterface $response): ResponseInterface
    {
        foreach ($this->responseDecorators as $decorator) {
            $response = $decorator($response);
        }

        return $response;
    }
}
