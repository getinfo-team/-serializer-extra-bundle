<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Mapping\Factory;

use GetInfoTeam\SerializerExtraBundle\Mapping\ClassMetadataInterface;
use GetInfoTeam\SerializerExtraBundle\Mapping\Loader\LoaderInterface;

class ClassMetadataFactory implements ClassMetadataFactoryInterface
{
    use ClassResolverTrait;

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
}