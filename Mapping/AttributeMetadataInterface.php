<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Mapping;

interface AttributeMetadataInterface
{
    public function getName(): string;

    public function isExclude(): bool;

    public function isExpose(): bool;

    public function getter(): ?string;

    public function setter(): ?string;

    public function getConverter(): ?string;

    public function getOptions(): array;
}