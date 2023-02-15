<?php

declare(strict_types=1);

namespace Fuel\Route\Strategy;

interface OptionsHandlerInterface
{
    public function getOptionsCallable(array $methods): callable;
}
