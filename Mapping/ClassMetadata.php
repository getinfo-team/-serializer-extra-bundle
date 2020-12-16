<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Mapping;

use GetInfoTeam\SerializerExtraBundle\Exception\Mapping\AttributeNotExistsException;
use GetInfoTeam\SerializerExtraBundle\Exception\Mapping\DuplicateAttributeNameException;
use GetInfoTeam\SerializerExtraBundle\Exception\Mapping\InvalidExclusionPolicyException;

class ClassMetadata implements ClassMetadataInterface
{
    /** @var string */
    private $class;

    /** @var AttributeMetadataInterface[] */
    private $properties = [];

    /** @var string */
    private $exclusionPolicy;

    /**
     * @param string $class
     * @param AttributeMetadataInterface[] $properties
     * @param string $exclusionPolicy
     */
    public function __construct(string $class, array $properties, string $exclusionPolicy = self::EXCLUSION_POLICY_NONE)
    {
        if (!in_array($exclusionPolicy, [static::EXCLUSION_POLICY_ALL, static::EXCLUSION_POLICY_NONE])) {
            throw new InvalidExclusionPolicyException();
        }

        $this->class = $class;
        $this->exclusionPolicy = $exclusionPolicy;

        foreach ($properties as $property) {
            if (isset($this->properties[$property->getName()])) {
                throw new DuplicateAttributeNameException($property->getName());
            }

            $this->properties[$property->getName()] = $property;
        }
    }

    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return array_values($this->properties);
    }

    public function getAttribute(string $name): AttributeMetadataInterface
    {
        if (!isset($this->properties[$name])) {
            throw new AttributeNotExistsException($name);
        }

        return $this->properties[$name];
    }


    public function getExclusionPolicy(): string
    {
        return $this->exclusionPolicy;
    }
}