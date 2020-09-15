<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Exception\Converter;

use GetInfoTeam\SerializerExtraBundle\Exception\InvalidArgumentException;
use Throwable;

class CollectionTypeValueNotIterableException extends InvalidArgumentException
{
    const MESSAGE = 'Value of collection type is not iterable.';

    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(static::MESSAGE, $code, $previous);
    }
}