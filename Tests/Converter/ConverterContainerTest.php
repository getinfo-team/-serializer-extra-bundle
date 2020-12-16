<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Tests\Converter;

use GetInfoTeam\SerializerExtraBundle\Converter\ConverterContainer;
use GetInfoTeam\SerializerExtraBundle\Converter\ConverterInterface;
use GetInfoTeam\SerializerExtraBundle\Exception\Converter\ConverterNotExistsException;
use GetInfoTeam\SerializerExtraBundle\Exception\Converter\DuplicateConverterNameException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConverterContainerTest extends TestCase
{
    public function test()
    {
        $converter = $this->createConverter('test');
        $container = new ConverterContainer([$converter]);

        $this->assertSame($converter, $container->get('test'));
        $this->assertTrue($container->has('test'));

        $this->assertFalse($container->has('invalid'));
    }

    public function testDuplicateConverterNameException()
    {
        $this->expectException(DuplicateConverterNameException::class);
        $this->expectExceptionMessage(sprintf(DuplicateConverterNameException::MESSAGE, 'test'));

        new ConverterContainer([
            $this->createConverter('test'),
            $this->createConverter('test')
        ]);
    }

    public function testConverterNotExistsException()
    {
        $this->expectException(ConverterNotExistsException::class);
        $this->expectExceptionMessage(sprintf(ConverterNotExistsException::MESSAGE, 'test'));

        $container = new ConverterContainer([]);

        $container->get('test');
    }

    private function createConverter(string $name): ConverterInterface
    {
        /** @var ConverterInterface|MockObject $converter */
        $converter = $this->getMockBuilder(ConverterInterface::class)
            ->onlyMethods(['getName'])
            ->getMockForAbstractClass()
        ;
        $converter->expects($this->any())->method('getName')->willReturn($name);

        return $converter;
    }
}
