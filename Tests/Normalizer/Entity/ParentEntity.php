<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Tests\Normalizer\Entity;

use GetInfoTeam\SerializerExtraBundle\Annotation\Accessor;
use GetInfoTeam\SerializerExtraBundle\Annotation\Converter;
use GetInfoTeam\SerializerExtraBundle\Annotation\Exclude;
use GetInfoTeam\SerializerExtraBundle\Annotation\Expose;
use Symfony\Component\Serializer\Annotation\SerializedName;

class ParentEntity
{
    public $simple = 'Simple';

    /**
     * @Converter(converter="test_converter")
     *
     * @var AssociatedEntity
     */
    public $converter;

    /**
     * @Exclude()
     */
    public $excluded = 'Excluded';

    /**
     * @Expose()
     */
    public $exposed = 'Exposed';

    /**
     * @Accessor(getter="getPublicProperty", setter="setPublicProperty")
     */
    protected $accessor;

    /**
     * @Accessor(getter="getMultiple", setter="setMultiple")
     * @Converter("test_converter")
     * @SerializedName("multiple")
     *
     * @var AssociatedEntity
     */
    protected $multipleAnnotation;

    private $privateProperty = 'Private';

    public function getPublicProperty()
    {
        return $this->accessor;
    }

    public function setPublicProperty($publicPropertyAccessor)
    {
        $this->accessor = $publicPropertyAccessor;
    }

    public function getMultiple()
    {
        return $this->multipleAnnotation;
    }

    public function setMultiple($value)
    {
        $this->multipleAnnotation = $value;
    }

    public function getPrivateProperty()
    {
        return $this->privateProperty;
    }

    public function setPrivateProperty(string $privateProperty)
    {
        $this->privateProperty = $privateProperty;
    }

    public function getVirtualProperty()
    {
        return 'Virtual property';
    }
}