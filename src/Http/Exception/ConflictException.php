<?php

declare(strict_types=1);

namespace Fuel\Route\Http\Exception;

use Exception;
use Fuel\Route\Http;

class ConflictException extends Http\Exception
{
    public function __construct(string $message = 'Conflict', ?Exception $previous = null, int $code = 0)
    {
        parent::__construct(409, $message, $previous, [], $code);
    }
}
