<?php

namespace Services\ExternalApi\Exceptions;

use Exception;

class ExternalApiException extends Exception
{
    public static function responseFailed(): self
    {
        return new self('External API response failed');
    }

    public static function validationFailed(): self
    {
        return new self('External API validation failed');
    }
}
