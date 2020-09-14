<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Mapping;

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
        $this->properties = $properties;
        $this->exclusionPolicy = $exclusionPolicy;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @inheritDoc
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getExclusionPolicy(): string
    {
        return $this->exclusionPolicy;
    }
}