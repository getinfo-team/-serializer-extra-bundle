<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Annotation;

use GetInfoTeam\SerializerExtraBundle\Mapping\ClassMetadataInterface;

/**
 * @Annotation
 * @Target("CLASS")
 */
class ExclusionPolicy
{
    /**
     * @Enum({ClassMetadataInterface::EXCLUSION_POLICY_ALL, ClassMetadataInterface::EXCLUSION_POLICY_NONE})
     */
    public $policy = ClassMetadataInterface::EXCLUSION_POLICY_NONE;
}