<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Tests\Mapping;

use GetInfoTeam\SerializerExtraBundle\Mapping\AttributeMetadataBuilder;
use PHPUnit\Framework\TestCase;

class AttributeMetadataBuilderTest extends TestCase
{
    public function test()
    {
        $attribute = AttributeMetadataBuilder::create('test')
            ->setExclude(true)
            ->setExpose(true)
            ->setGetter('getTest')
            ->setSetter('setTest')
            ->setConverter('testConverter')
            ->setOptions(['foo' => 123, 'bar' => 456])
            ->build()
        ;

        $this->assertSame('test', $attribute->getName());
        $this->assertTrue($attribute->isExclude());
        $this->assertTrue($attribute->isExpose());
        $this->assertSame('getTest', $attribute->getter());
        $this->assertSame('setTest', $attribute->setter());
        $this->assertSame('testConverter', $attribute->getConverter());
        $this->assertSame(['foo' => 123, 'bar' => 456], $attribute->getOptions());
    }

    public function testDefault()
    {
        $attribute = AttributeMetadataBuilder::create('test')->build();

        $this->assertSame('test', $attribute->getName());
        $this->assertFalse($attribute->isExclude());
        $this->assertFalse($attribute->isExpose());
        $this->assertNull($attribute->getter());
        $this->assertNull($attribute->setter());
        $this->assertNull($attribute->getConverter());
        $this->assertSame([], $attribute->getOptions());
    }
}
