<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Exception\Converter;

use GetInfoTeam\SerializerExtraBundle\Exception\LogicException;
use Throwable;

class NoIdentifierException extends LogicException
{
    const MESSAGE = 'Class "%s" doesn\'t have identifier.';

    public function __construct(string $class, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf(static::MESSAGE, $class), $code, $previous);
    }
}