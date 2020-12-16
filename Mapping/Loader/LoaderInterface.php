<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Mapping\Loader;

use GetInfoTeam\SerializerExtraBundle\Mapping\ClassMetadataInterface;

interface LoaderInterface
{
    public function load(string $class): ClassMetadataInterface;
}