<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Tests\Normalizer;

use Doctrine\Common\Annotations\AnnotationReader;
use GetInfoTeam\SerializerExtraBundle\Converter\ConverterContainer;
use GetInfoTeam\SerializerExtraBundle\Converter\ConverterInterface;
use GetInfoTeam\SerializerExtraBundle\Mapping\Factory\ClassMetadataFactory;
use GetInfoTeam\SerializerExtraBundle\Mapping\Loader\AnnotationLoader;
use GetInfoTeam\SerializerExtraBundle\Normalizer\ExtraObjectNormalizer;
use GetInfoTeam\SerializerExtraBundle\Tests\Normalizer\Entity\ExcludeEntity;
use GetInfoTeam\SerializerExtraBundle\Tests\Normalizer\Entity\ExposeEntity;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

class ExtraObjectNormalizerTest extends TestCase
{
    /** @var ConverterInterface|MockObject */
    private $converter;

    /** @var ExtraObjectNormalizer */
    private $normalizer;

    public function testSupportsNormalization()
    {
        $this->assertTrue($this->normalizer->supportsNormalization(new ExcludeEntity()));
        $this->assertTrue($this->normalizer->supportsNormalization(new ExposeEntity()));
        $this->assertFalse($this->normalizer->supportsNormalization(new stdClass()));
        $this->assertFalse($this->normalizer->supportsNormalization('scalar'));
    }

    protected function setUp(): void
    {
        /** @var ConverterInterface|MockObject $converter */
        $converter = $this->getMockBuilder(ConverterInterface::class)
            ->onlyMethods(['getName', 'convert', 'reconvert'])
            ->getMockForAbstractClass()
        ;
        $converter->expects($this->any())->method('getName')->willReturn('test_converter');
        $this->converter = $converter;

        $this->normalizer = new ExtraObjectNormalizer(
            new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader())),
            new ConverterContainer([$converter])
        );
    }
}
