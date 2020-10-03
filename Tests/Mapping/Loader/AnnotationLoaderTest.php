<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Tests\Mapping\Loader;

use Doctrine\Common\Annotations\AnnotationReader;
use GetInfoTeam\SerializerExtraBundle\Exception\Mapping\NotExtraSerializedException;
use GetInfoTeam\SerializerExtraBundle\Mapping\AttributeMetadataInterface;
use GetInfoTeam\SerializerExtraBundle\Mapping\ClassMetadataInterface;
use GetInfoTeam\SerializerExtraBundle\Mapping\Loader\AnnotationLoader;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use stdClass;

class AnnotationLoaderTest extends TestCase
{
    private $loader;

    /**
     * @throws ReflectionException
     */
    public function test()
    {
        $metadata = $this->loader->load(AnnotationEntity::class);

        $this->assertSame(AnnotationEntity::class, $metadata->getClass());
        $this->assertSame(ClassMetadataInterface::EXCLUSION_POLICY_NONE, $metadata->getExclusionPolicy());
        $this->assertCount(5, $metadata->getAttributes());

        $this->assertAttribute($metadata->getAttribute('virtual'), 'virtual', false, false, 'virtual');
        $this->assertAttribute($metadata->getAttribute('foo'), 'foo', false, true);
        $this->assertAttribute(
            $metadata->getAttribute('bar'),
            'bar',
            false,
            false,
            null,
            null,
            'testConverter',
            ['foo' => 123, 'bar' => 456]
        );
        $this->assertAttribute($metadata->getAttribute('baz'), 'baz', false, false, 'baz', 'setBaz');
        $this->assertAttribute($metadata->getAttribute('exclude'), 'exclude', true);
    }

    /**
     * @throws ReflectionException
     */
    public function testNotExtraSerializedException()
    {
        $this->expectException(NotExtraSerializedException::class);

        $this->loader->load(stdClass::class);
    }

    protected function setUp(): void
    {
        $this->loader = new AnnotationLoader(new AnnotationReader());
    }

    private function assertAttribute(
        AttributeMetadataInterface $attribute,
        ?string $name,
        bool $exclude = false,
        bool $expose = false,
        ?string $getter = null,
        ?string $setter = null,
        ?string $converter = null,
        array $options = []
    )
    {
        $this->assertSame($name, $attribute->getName());
        $this->assertSame($exclude, $attribute->isExclude());
        $this->assertSame($expose, $attribute->isExpose());
        $this->assertSame($getter, $attribute->getter());
        $this->assertSame($setter, $attribute->setter());
        $this->assertSame($converter, $attribute->getConverter());
        $this->assertSame($options, $attribute->getOptions());
    }
}
