<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class VirtualAttribute
{
    /**
     * @Required
     *
     * @var string|null
     */
    public $name = null;

    /** @var bool */
    public $exclude = false;

    /** @var bool */
    public $expose = false;

    /** @var string|null */
    public $getter = null;

    /** @var string|null */
    public $setter = null;

    /** @var string|null */
    public $converter = null;

    /** @var array */
    public $options = [];
}