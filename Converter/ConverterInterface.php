<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Converter;

interface ConverterInterface
{
    public function getName(): string;

    /**
     * @param object $object
     * @param string $attribute
     * @param mixed $value
     * @param array $options
     * @return mixed
     */
    public function convert($object, string $attribute, $value, array $options);

    /**
     * @param object $object
     * @param string $attribute
     * @param mixed $value
     * @param array $options
     * @return mixed
     */
    public function reconvert($object, string $attribute, $value, array $options);
}