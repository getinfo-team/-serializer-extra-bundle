<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Mapping;

interface ClassMetadataInterface
{
    const EXCLUSION_POLICY_ALL = 'ALL';
    const EXCLUSION_POLICY_NONE = 'NONE';

    public function getClass(): string;

    /**
     * @return AttributeMetadataInterface[]
     */
    public function getAttributes(): array;

    public function getAttribute(string $name): AttributeMetadataInterface;

    public function getExclusionPolicy(): string;
}