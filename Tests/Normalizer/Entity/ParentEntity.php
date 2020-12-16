<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Tests\Normalizer\Entity;

use GetInfoTeam\SerializerExtraBundle\Annotation\Accessor;
use GetInfoTeam\SerializerExtraBundle\Annotation\Converter;
use GetInfoTeam\SerializerExtraBundle\Annotation\Exclude;
use GetInfoTeam\SerializerExtraBundle\Annotation\Expose;
use Symfony\Component\Serializer\Annotation\Groups;

class ParentEntity
{
    public $simple;

    /**
     * @Converter(converter="testConverter")
     *
     * @var AssociatedEntity
     */
    public $converter;

    /**
     * @Exclude()
     */
    public $excluded;

    /**
     * @Expose()
     */
    public $exposed;

    /**
     * @Accessor(getter="_getPublicProperty", setter="_setPublicProperty")
     *
     * @Groups({"group"})
     */
    protected $accessor;

    /**
     * @Accessor(getter="_getMultiple", setter="_setMultiple")
     * @Converter("testConverter")
     *
     * @var AssociatedEntity
     */
    protected $multipleAnnotation;

    /**
     * @Groups({"group"})
     */
    private $privateProperty;

    public function _getPublicProperty()
    {
        return $this->accessor;
    }

    public function _setPublicProperty($publicPropertyAccessor)
    {
        $this->accessor = $publicPropertyAccessor;
    }

    public function _getMultiple()
    {
        return $this->multipleAnnotation;
    }

    public function _setMultiple($value)
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
        return 'VirtualProperty';
    }
}