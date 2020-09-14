<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Converter;

use GetInfoTeam\SerializerExtraBundle\Exception\Converter\ConverterNotExistsException;
use GetInfoTeam\SerializerExtraBundle\Exception\Converter\DuplicateConverterNameException;

class ConverterContainer implements ConverterContainerInterface
{
    /** @var ConverterInterface[] */
    private $converters = [];
    /**
     * @param ConverterInterface[] $converters
     */
    public function __construct(iterable $converters)
    {
        foreach ($converters as $converter) {
            if (isset($this->converters[$converter->getName()])) {
                throw new DuplicateConverterNameException($converter->getName());
            }

            $this->converters[$converter->getName()] = $converter;
        }
    }

    public function get(string $name): ConverterInterface
    {
        if (!$this->has($name)) {
            throw new ConverterNotExistsException($name);
        }

        return $this->converters[$name];
    }

    public function has(string $name): bool
    {
        return isset($this->converters[$name]);
    }
}