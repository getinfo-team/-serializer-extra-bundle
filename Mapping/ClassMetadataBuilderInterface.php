<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Mapping;

interface ClassMetadataBuilderInterface
{
    public static function create(string $class): self;

    /**
     * @param AttributeMetadataInterface[] $attributes
     * @return $this
     */
    public function addAttributes(array $attributes): self;

    public function addAttribute(AttributeMetadataInterface $attribute): self;

    public function setExclusionPolicy(string $policy): self;

    public function build(): ClassMetadataInterface;
}