<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Exception\Metadata;

use GetInfoTeam\SerializerExtraBundle\Exception\InvalidArgumentException;
use Throwable;

class InvalidExclusionPolicyException extends InvalidArgumentException
{
    const MESSAGE = 'Invalid exclusion policy.';

    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(static::MESSAGE, $code, $previous);
    }
}