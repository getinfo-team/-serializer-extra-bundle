<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Tests\Mapping\Factory;

use GetInfoTeam\SerializerExtraBundle\Mapping\ClassMetadataBuilder;
use GetInfoTeam\SerializerExtraBundle\Mapping\Factory\ClassMetadataFactory;
use GetInfoTeam\SerializerExtraBundle\Mapping\Loader\LoaderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

class ClassMetadataFactoryTest extends TestCase
{
    public function test()
    {
        $metadata = ClassMetadataBuilder::create(stdClass::class)->build();

        /** @var LoaderInterface|MockObject $loader */
        $loader = $this->getMockBuilder(LoaderInterface::class)->onlyMethods(['load'])->getMockForAbstractClass();
        $loader->expects($this->any())->method('load')->willReturn($metadata);

        $factory = new ClassMetadataFactory($loader);

        $this->assertSame($metadata, $factory->getMetadataFor(stdClass::class));
    }
}
