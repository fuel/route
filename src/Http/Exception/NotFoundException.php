<?php

declare(strict_types=1);

namespace Fuel\Route\Http\Exception;

use Exception;
use Fuel\Route\Http;

class NotFoundException extends Http\Exception
{
    public function __construct(string $message = 'Not Found', ?Exception $previous = null, int $code = 0)
    {
        parent::__construct(404, $message, $previous, [], $code);
    }
}
