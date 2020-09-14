<?php

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