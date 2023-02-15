<?php

declare(strict_types=1);

namespace Fuel\Route\Strategy;

trait StrategyAwareTrait
{
    /**
     * @var ?StrategyInterface
     */
    protected $strategy;

    public function setStrategy(StrategyInterface $strategy): StrategyAwareInterface
    {
        $this->strategy = $strategy;
        return $this;
    }

    public function getStrategy(): ?StrategyInterface
    {
        return $this->strategy;
    }
}
