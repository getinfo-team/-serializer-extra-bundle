<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Tests\Normalizer\Entity;

use GetInfoTeam\SerializerExtraBundle\Annotation\ExtraSerialized;
use GetInfoTeam\SerializerExtraBundle\Annotation\VirtualAttribute;

/**
 * @ExtraSerialized(
 *     properties={
 *         @VirtualAttribute(name="privateProperty"),
 *         @VirtualAttribute(name="virtual", getter="getVirtualProperty")
 *     }
 * )
 */
class ExcludeEntity extends ParentEntity
{
    public $foo;
}