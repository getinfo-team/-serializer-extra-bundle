<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Mapping;

class AttributeMetadataBuilder implements AttributeMetadataBuilderInterface
{
    /** @var string */
    private $name;

    /** @var bool */
    private $exclude = false;

    /** @var bool */
    private $expose = false;

    /** @var string|null */
    private $getter = null;

    /** @var string|null */
    private $setter = null;

    /** @var string|null */
    private $converter = null;

    /** @var array */
    private $options = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function create(string $name): AttributeMetadataBuilderInterface
    {
        return new static($name);
    }

    public function setExclude(bool $exclude): AttributeMetadataBuilderInterface
    {
        $this->exclude = $exclude;

        return $this;
    }

    public function setExpose(bool $expose): AttributeMetadataBuilderInterface
    {
        $this->expose = $expose;

        return $this;
    }

    public function setGetter(?string $getter): AttributeMetadataBuilderInterface
    {
        $this->getter = $getter;

        return $this;
    }

    public function setSetter(?string $setter): AttributeMetadataBuilderInterface
    {
        $this->setter = $setter;

        return $this;
    }

    public function setConverter(?string $converter): AttributeMetadataBuilderInterface
    {
        $this->converter = $converter;

        return $this;
    }

    public function setOptions(array $options): AttributeMetadataBuilderInterface
    {
        $this->options = $options;

        return $this;
    }

    public function build(): AttributeMetadataInterface
    {
        return new AttributeMetadata(
            $this->name,
            $this->exclude,
            $this->expose,
            $this->getter,
            $this->setter,
            $this->converter,
            $this->options
        );
    }
}