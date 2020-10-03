<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Tests\Mapping\Loader;

use GetInfoTeam\SerializerExtraBundle\Annotation\Accessor;
use GetInfoTeam\SerializerExtraBundle\Annotation\Converter;
use GetInfoTeam\SerializerExtraBundle\Annotation\Exclude;
use GetInfoTeam\SerializerExtraBundle\Annotation\Expose;
use GetInfoTeam\SerializerExtraBundle\Annotation\ExtraSerialized;
use GetInfoTeam\SerializerExtraBundle\Annotation\VirtualAttribute;

/**
 * @ExtraSerialized(
 *     properties={
 *         @VirtualAttribute(name="virtual", getter="virtual")
 *     }
 * )
 */
class AnnotationEntity
{
    /**
     * @Expose()
     */
    public $foo = 'Foo';

    /**
     * @Converter(converter="testConverter", options={"foo": 123, "bar": 456})
     */
    public $bar = 'Bar';

    /**
     * @Accessor(getter="baz", setter="setBaz")
     */
    private $baz = 'Baz';

    /**
     * @Exclude()
     */
    public $exclude = 'Exclude';

    public function baz()
    {
        return $this->baz;
    }

    public function setBaz($baz)
    {
        $this->baz = $baz;
    }

    public function virtual()
    {
        return 'Virtual';
    }
}