<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Exception\Converter;

use GetInfoTeam\SerializerExtraBundle\Exception\LogicException;
use Throwable;

class MultiColumnIdentifierException extends LogicException
{
    const MESSAGE = 'Class "%s" has multi-column identifier.';

    public function __construct(string $class, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf(static::MESSAGE, $class), $code, $previous);
    }
}