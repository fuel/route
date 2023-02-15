<?php

declare(strict_types=1);

namespace Fuel\Route\Http\Exception;

use Exception;
use Fuel\Route\Http;

class UnsupportedMediaException extends Http\Exception
{
    public function __construct(string $message = 'Unsupported Media', ?Exception $previous = null, int $code = 0)
    {
        parent::__construct(415, $message, $previous, [], $code);
    }
}
