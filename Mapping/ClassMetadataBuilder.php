<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Mapping;

class ClassMetadataBuilder implements ClassMetadataBuilderInterface
{
    /** @var string */
    private $class;

    /** @var AttributeMetadataInterface[] */
    private $attributes = [];

    /** @var string */
    private $policy = ClassMetadataInterface::EXCLUSION_POLICY_NONE;

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public static function create(string $class): ClassMetadataBuilderInterface
    {
        return new static($class);
    }

    /**
     * @inheritDoc
     */
    public function addAttributes(array $attributes): ClassMetadataBuilderInterface
    {
        foreach ($attributes as $attribute) {
            $this->addAttribute($attribute);
        }

        return $this;
    }

    public function addAttribute(AttributeMetadataInterface $attribute): ClassMetadataBuilderInterface
    {
        $this->attributes[] = $attribute;

        return $this;
    }

    public function setExclusionPolicy(string $policy): ClassMetadataBuilderInterface
    {
        $this->policy = $policy;

        return $this;
    }

    public function build(): ClassMetadataInterface
    {
        return new ClassMetadata($this->class, $this->attributes, $this->policy);
    }
}