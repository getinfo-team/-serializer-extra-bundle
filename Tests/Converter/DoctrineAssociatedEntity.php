<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Tests\Converter;

class DoctrineAssociatedEntity
{
    public $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
}