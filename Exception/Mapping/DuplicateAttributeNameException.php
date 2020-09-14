<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Exception\Mapping;

use GetInfoTeam\SerializerExtraBundle\Exception\LogicException;
use Throwable;

class DuplicateAttributeNameException extends LogicException
{
    const MESSAGE = 'Attribute with name "%s" already exists.';

    public function __construct(string $attribute, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf(static::MESSAGE, $attribute), $code, $previous);
    }
}