<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use GetInfoTeam\SerializerExtraBundle\Mapping\ClassMetadataInterface;

/**
 * @Annotation
 * @Target("CLASS")
 */
class ExtraSerialized
{
    /**
     * @Enum({ClassMetadataInterface::EXCLUSION_POLICY_ALL, ClassMetadataInterface::EXCLUSION_POLICY_NONE})
     */
    public $policy = ClassMetadataInterface::EXCLUSION_POLICY_NONE;

    /**
     * @var array<VirtualAttribute>|VirtualAttribute[]
     */
    public $properties = [];
}