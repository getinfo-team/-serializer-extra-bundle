<?php

namespace GetInfoTeam\SerializerExtraBundle\Mapping;

interface AttributeMetadataBuilderInterface
{
    public static function create(string $name): self;

    public function setExclude(bool $exclude): self;

    public function setExpose(bool $expose): self;

    public function setGetter(?string $getter): self;

    public function setSetter(?string $setter): self;

    public function setConverter(?string $converter): self;

    public function setOptions(array $options): self;

    public function build(): AttributeMetadataInterface;
}