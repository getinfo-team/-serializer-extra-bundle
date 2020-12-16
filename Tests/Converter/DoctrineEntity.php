<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Tests\Converter;

class DoctrineEntity
{
    /**
     * @var DoctrineAssociatedEntity|null
     */
    public $association;

    /**
     * @var DoctrineAssociatedEntity[]
     */
    public $collectionAssociations = [];

    public $typeNotResolve;

    /** @var string|int|float */
    public $multipleTypes;
}