<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Tests\Mapping\Loader;

use GetInfoTeam\SerializerExtraBundle\Annotation\Exclude;
use GetInfoTeam\SerializerExtraBundle\Annotation\ExtraSerialized;

/**
 * @ExtraSerialized(policy="ALL")
 */
class AnnotationEntityParent
{
    /**
     * @Exclude()
     */
    protected $parent;

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }
}