<?php

declare(strict_types=1);

namespace Fuel\Route\Http\Exception;

use Exception;
use Fuel\Route\Http;

class PreconditionRequiredException extends Http\Exception
{
    public function __construct(string $message = 'Precondition Required', ?Exception $previous = null, int $code = 0)
    {
        parent::__construct(428, $message, $previous, [], $code);
    }
}
