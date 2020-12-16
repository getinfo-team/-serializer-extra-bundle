<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Exception\Mapping;

use GetInfoTeam\SerializerExtraBundle\Exception\RuntimeException;
use Throwable;

class AttributeNotExistsException extends RuntimeException
{
    const MESSAGE = 'Attribute "%s" not exists.';

    public function __construct(string $attribute, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf(static::MESSAGE, $attribute), $code, $previous);
    }
}