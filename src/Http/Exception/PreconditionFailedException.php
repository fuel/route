<?php

declare(strict_types=1);

namespace Fuel\Route\Http\Exception;

use Exception;
use Fuel\Route\Http;

class PreconditionFailedException extends Http\Exception
{
    public function __construct(string $message = 'Precondition Failed', ?Exception $previous = null, int $code = 0)
    {
        parent::__construct(412, $message, $previous, [], $code);
    }
}
