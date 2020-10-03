<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Tests\Mapping\Factory;

use DateTime;
use GetInfoTeam\SerializerExtraBundle\Exception\InvalidArgumentException;
use GetInfoTeam\SerializerExtraBundle\Mapping\Factory\ClassResolverTrait;
use PHPUnit\Framework\TestCase;

class ClassResolverTraitTest extends TestCase
{
    private $trait;

    public function test()
    {
        $this->assertSame(DateTime::class, $this->trait->getClassMethod(DateTime::class));
        $this->assertSame(DateTime::class, $this->trait->getClassMethod('\DateTime'));
        $this->assertSame(DateTime::class, $this->trait->getClassMethod(new DateTime()));
    }

    public function testClassNotExists()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The class or interface "Class" does not exist.');

        $this->trait->getClassMethod('Class');
    }

    public function testInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot create metadata for non-objects. Got: "int".');

        $this->trait->getClassMethod(123);
    }

    protected function setUp(): void
    {
        $this->trait = new class {
            use ClassResolverTrait;

            public function getClassMethod($value)
            {
                return $this->getClass($value);
            }
        };
    }
}