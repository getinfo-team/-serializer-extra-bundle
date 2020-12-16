<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Accessor
{
    /** @var string */
    public $getter = null;

    /** @var string */
    public $setter = null;
}