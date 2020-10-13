<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Tests\Normalizer\Entity;

use GetInfoTeam\SerializerExtraBundle\Annotation\ExtraSerialized;
use GetInfoTeam\SerializerExtraBundle\Annotation\VirtualAttribute;

/**
 * @ExtraSerialized(
 *     policy="ALL",
 *     properties={
 *         @VirtualAttribute(name="privateProperty"),
 *         @VirtualAttribute(name="virtual", getter="getVirtualProperty")
 *     }
 * )
 */
class ExposeEntity extends ParentEntity
{
    public $foo = 'Foo';
}