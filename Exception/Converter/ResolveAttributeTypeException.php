<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Exception\Converter;

use GetInfoTeam\SerializerExtraBundle\Exception\LogicException;
use Throwable;

class ResolveAttributeTypeException extends LogicException
{
    const MESSAGE = 'Can not resolve type of attribute "%s::$%s"';

    public function __construct(string $class, string $attribute, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf(static::MESSAGE, $class, $attribute), $code, $previous);
    }
}