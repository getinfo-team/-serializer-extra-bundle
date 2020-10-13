<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Tests\Normalizer\Entity;

class AssociatedEntity
{
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }
}