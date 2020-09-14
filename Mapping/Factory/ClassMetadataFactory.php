<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Mapping\Factory;

use GetInfoTeam\SerializerExtraBundle\Exception\InvalidArgumentException;
use GetInfoTeam\SerializerExtraBundle\Mapping\ClassMetadataInterface;
use GetInfoTeam\SerializerExtraBundle\Mapping\Loader\LoaderInterface;
use function get_class;
use function is_object;
use function is_string;

class ClassMetadataFactory implements ClassMetadataFactoryInterface
{
    /** @var LoaderInterface */
    private $loader;

    /** @var ClassMetadataInterface[] */
    private $loadedClasses = [];

    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    public function getMetadataFor($value): ClassMetadataInterface
    {
        $class = $this->getClass($value);

        if (!isset($this->loadedClasses[$class])) {
            $this->loadedClasses[$class] = $this->loader->load($class);
        }

        return $this->loadedClasses[$class];
    }

    /**
     * @param string|object $value
     * @return string
     */
    protected function getClass($value): string
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