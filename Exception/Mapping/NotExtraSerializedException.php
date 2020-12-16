<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Exception\Mapping;

use GetInfoTeam\SerializerExtraBundle\Exception\LogicException;
use Throwable;

class NotExtraSerializedException extends LogicException
{
    const MESSAGE = 'Class "%s" is not extra serialized.';

    public function __construct(string $class, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf(static::MESSAGE, $class), $code, $previous);
    }
}