<?php

declare(strict_types=1);

namespace Fuel\Route\Http\Exception;

use Exception;
use Fuel\Route\Http;

class BadRequestException extends Http\Exception
{
    public function __construct(string $message = 'Bad Request', ?Exception $previous = null, int $code = 0)
    {
        parent::__construct(400, $message, $previous, [], $code);
    }
}
