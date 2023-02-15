<?php

declare(strict_types=1);

namespace Fuel\Route\Strategy;

interface StrategyAwareInterface
{
    public function getStrategy(): ?StrategyInterface;
    public function setStrategy(StrategyInterface $strategy): StrategyAwareInterface;
}
