<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Mapping\Factory;

use GetInfoTeam\SerializerExtraBundle\Exception\InvalidArgumentException;

trait ClassResolverTrait
{
    /**
     * @param string|object $value
     * @return string
     */
    private function getClass($value): string
    {
        if (is_string($value)) {
            if (!class_exists($value) && !interface_exists($value, false)) {
                throw new InvalidArgumentException(sprintf('The class or interface "%s" does not exist.', $value));
            }

            return ltrim($value, '\\');
        }

        if (!is_object($value)) {
            throw new InvalidArgumentException(sprintf('Cannot create metadata for non-objects. Got: "%s".', get_debug_type($value)));
        }

        return get_class($value);
    }
}