<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Tests\Normalizer\Entity;

use GetInfoTeam\SerializerExtraBundle\Annotation\ExtraSerialized;
use GetInfoTeam\SerializerExtraBundle\Annotation\VirtualAttribute;

/**
 * @ExtraSerialized(
 *     policy="ALL",
 *     properties={
 *         @VirtualAttribute(name="privateProperty", expose=true),
 *         @VirtualAttribute(name="virtual", getter="getVirtualProperty", expose=true)
 *     }
 * )
 */
class ExposeEntity extends ParentEntity
{
    public $foo;
}