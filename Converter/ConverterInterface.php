<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Converter;

interface ConverterInterface
{
    public function getName(): string;

    /**
     * @param object $object
     * @param string $attribute
     * @param array $options
     * @return mixed
     */
    public function convert($object, string $attribute, array $options);

    /**
     * @param object $object
     * @param string $attribute
     * @param array $options
     * @return mixed
     */
    public function reconvert($object, string $attribute, array $options);
}