<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Annotation;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
class VirtualAttribute
{
    /**
     * @Required
     *
     * @var string
     */
    public $name = null;

    /** @var bool */
    public $exclude = null;

    /** @var bool */
    public $expose = null;

    /** @var string */
    public $getter = null;

    /** @var string */
    public $setter = null;

    /** @var string */
    public $converter = null;

    /** @var array */
    public $options = null;
}