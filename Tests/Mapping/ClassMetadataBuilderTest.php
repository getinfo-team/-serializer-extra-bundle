<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Tests\Mapping;

use GetInfoTeam\SerializerExtraBundle\Exception\Mapping\AttributeNotExistsException;
use GetInfoTeam\SerializerExtraBundle\Exception\Mapping\DuplicateAttributeNameException;
use GetInfoTeam\SerializerExtraBundle\Exception\Mapping\InvalidExclusionPolicyException;
use GetInfoTeam\SerializerExtraBundle\Mapping\AttributeMetadata;
use GetInfoTeam\SerializerExtraBundle\Mapping\ClassMetadataBuilder;
use GetInfoTeam\SerializerExtraBundle\Mapping\ClassMetadataInterface;
use PHPUnit\Framework\TestCase;

class ClassMetadataBuilderTest extends TestCase
{
    public function test()
    {
        $attribute = new AttributeMetadata('prop');

        $metadata = ClassMetadataBuilder::create('Class')
            ->addAttributes([$attribute])
            ->setExclusionPolicy(ClassMetadataInterface::EXCLUSION_POLICY_ALL)
            ->build()
        ;

        $this->assertSame('Class', $metadata->getClass());
        $this->assertSame([$attribute], $metadata->getAttributes());
        $this->assertSame($attribute, $metadata->getAttribute('prop'));
        $this->assertSame(ClassMetadataInterface::EXCLUSION_POLICY_ALL, $metadata->getExclusionPolicy());
    }

    public function testDefault()
    {
        $metadata = ClassMetadataBuilder::create('Class')->build();

        $this->assertSame('Class', $metadata->getClass());
        $this->assertSame([], $metadata->getAttributes());
        $this->assertSame(ClassMetadataInterface::EXCLUSION_POLICY_NONE, $metadata->getExclusionPolicy());
    }

    public function testInvalidExclusionPolicyException()
    {
        $this->expectException(InvalidExclusionPolicyException::class);
        $this->expectExceptionMessage(InvalidExclusionPolicyException::MESSAGE);

        ClassMetadataBuilder::create('Class')
            ->setExclusionPolicy('invalid')
            ->build()
        ;
    }

    public function testDuplicateAttributeNameException()
    {
        $this->expectException(DuplicateAttributeNameException::class);
        $this->expectExceptionMessage(sprintf(DuplicateAttributeNameException::MESSAGE, 'foo'));

        ClassMetadataBuilder::create('Class')
            ->addAttributes([new AttributeMetadata('foo'), new AttributeMetadata('foo')])
            ->build()
        ;
    }

    public function testAttributeNotExistsException()
    {
        $this->expectException(AttributeNotExistsException::class);
        $this->expectExceptionMessage(sprintf(AttributeNotExistsException::MESSAGE, 'foo'));

        $metadata = ClassMetadataBuilder::create('Class')->build();

        $metadata->getAttribute('foo');
    }
}
