<?php

namespace GetInfoTeam\SerializerExtraBundle\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Accessor
{
    /** @var string|null */
    public $getter = null;

    /** @var string|null */
    public $setter = null;
}