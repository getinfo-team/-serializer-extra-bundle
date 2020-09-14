<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Converter
{
    /** @var string|null */
    public $converter = null;

    /** @var array */
    public $options = [];
}