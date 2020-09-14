<?php

namespace GetInfoTeam\SerializerExtraBundle\Mapping\Loader;

use GetInfoTeam\SerializerExtraBundle\Mapping\ClassMetadataInterface;

interface LoaderInterface
{
    public function load(string $class): ClassMetadataInterface;
}