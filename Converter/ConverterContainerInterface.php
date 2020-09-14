<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Converter;

interface ConverterContainerInterface
{
    public function get(string $name): ConverterInterface;

    public function has(string $name): bool;
}