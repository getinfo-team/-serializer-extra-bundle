<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Tests\Normalizer;

use Doctrine\Common\Annotations\AnnotationReader;
use GetInfoTeam\SerializerExtraBundle\Annotation\Accessor;
use GetInfoTeam\SerializerExtraBundle\Annotation\ExtraSerialized;
use GetInfoTeam\SerializerExtraBundle\Converter\ConverterContainer;
use GetInfoTeam\SerializerExtraBundle\Converter\ConverterInterface;
use GetInfoTeam\SerializerExtraBundle\Mapping\Factory\ClassMetadataFactory;
use GetInfoTeam\SerializerExtraBundle\Mapping\Loader\AnnotationLoader;
use GetInfoTeam\SerializerExtraBundle\Normalizer\ExtraObjectNormalizer;
use GetInfoTeam\SerializerExtraBundle\Tests\Normalizer\Entity\AssociatedEntity;
use GetInfoTeam\SerializerExtraBundle\Tests\Normalizer\Entity\ExcludeEntity;
use GetInfoTeam\SerializerExtraBundle\Tests\Normalizer\Entity\ExposeEntity;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\LogicException;

class ExtraObjectNormalizerTest extends TestCase
{
    /** @var ExtraObjectNormalizer */
    private $normalizer;

    public function testSupportsNormalization()
    {
        $this->assertTrue($this->normalizer->supportsNormalization(new ExcludeEntity()));
        $this->assertTrue($this->normalizer->supportsNormalization(new ExposeEntity()));
        $this->assertFalse($this->normalizer->supportsNormalization(new stdClass()));
        $this->assertFalse($this->normalizer->supportsNormalization('scalar'));
    }

    public function testSupportsDenormalization()
    {
        $this->assertTrue($this->normalizer->supportsDenormalization([], ExcludeEntity::class));
        $this->assertTrue($this->normalizer->supportsDenormalization([], ExposeEntity::class));
        $this->assertFalse($this->normalizer->supportsDenormalization([], stdClass::class));
        $this->assertFalse($this->normalizer->supportsDenormalization('123','int'));
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeExclusionPolicyNone()
    {
        $entity = $this->createEntity(ExcludeEntity::class);

        $this->assertSame(
            [
                'foo' => 'Foo',
                'simple' => 'Simple',
                'converter' => 'converter',
                'exposed' => 'Exposed',
                'accessor' => 'AccessorValue',
                'multipleAnnotation' => 'multiple',
                'privateProperty' => 'Private',
                'virtual' => 'VirtualProperty'
            ],
            $this->normalizer->normalize($entity)
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeExclusionPolicyAll()
    {
        $entity = $this->createEntity(ExposeEntity::class);

        $this->assertSame(
            [
                'exposed' => 'Exposed',
                'privateProperty' => 'Private',
                'virtual' => 'VirtualProperty'
            ],
            $this->normalizer->normalize($entity)
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeWithGroups()
    {
        $entity = $this->createEntity(ExcludeEntity::class);

        $this->assertSame(
            [
                'accessor' => 'AccessorValue',
                'privateProperty' => 'Private',
            ],
            $this->normalizer->normalize($entity, null, ['groups' => ['group']])
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeWithAttributes()
    {
        $this->assertSame(
            [
                'foo' => 'Foo',
                'simple' => 'Simple',
                'multipleAnnotation' => 'multiple',
                'virtual' => 'VirtualProperty'
            ],
            $this->normalizer->normalize(
                $this->createEntity(ExcludeEntity::class),
                null,
                [
                    'attributes' => ['foo', 'simple', 'multipleAnnotation', 'virtual']
                ]
            )
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeWithIgnoredAttributes()
    {
        $this->assertSame(
            [
                'foo' => 'Foo',
                'simple' => 'Simple'
            ],
            $this->normalizer->normalize(
                $this->createEntity(ExcludeEntity::class),
                null,
                [
                    'ignored_attributes' => [
                        'converter',
                        'excluded',
                        'exposed',
                        'accessor',
                        'multipleAnnotation',
                        'privateProperty',
                        'virtual'
                    ],
                ]
            )
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeLogicExceptionGetterHasRequiredParameter()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Getter of attribute "foo" has required arguments.');

        /**
         * @ExtraSerialized()
         */
        $obj = new class {
            /**
             * @Accessor(getter="foo")
             */
            private $foo;

            public function foo($arg)
            {
                return $this->foo . $arg;
            }
        };

        $this->normalizer->normalize($obj);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeLogicExceptionGetterNotPublic()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Getter of attribute "foo" is not public.');

        /**
         * @ExtraSerialized()
         */
        $obj = new class {
            /**
             * @Accessor(getter="foo")
             */
            private $foo;

            protected function foo()
            {
                return $this->foo;
            }
        };

        $this->normalizer->normalize($obj);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeLogicExceptionGetterIsStatic()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Getter of attribute "foo" is static.');

        /**
         * @ExtraSerialized()
         */
        $obj = new class {
            /**
             * @Accessor(getter="foo")
             */
            protected $foo;

            public static function foo()
            {
                return '';
            }
        };

        $this->normalizer->normalize($obj);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeLogicExceptionGetterIsConstructor()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Getter of attribute "foo" is constructor.');

        /**
         * @ExtraSerialized()
         */
        $obj = new class {
            /**
             * @Accessor(getter="__construct")
             */
            protected $foo;

            public function __construct() {}
        };

        $this->normalizer->normalize($obj);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeLogicExceptionGetterIsDestructor()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Getter of attribute "foo" is destructor.');

        /**
         * @ExtraSerialized()
         */
        $obj = new class {
            /**
             * @Accessor(getter="__destruct")
             */
            protected $foo;

            public function __destruct() {}
        };

        $this->normalizer->normalize($obj);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testDenormalizeExclusionPolicyNone()
    {
        /** @var ExcludeEntity $obj */
        $obj = $this->normalizer->denormalize($this->createData(), ExcludeEntity::class);

        $this->assertInstanceOf(ExcludeEntity::class, $obj);

        $this->assertSame('Foo', $obj->foo);
        $this->assertSame('Simple', $obj->simple);
        $this->assertSame('converter', $obj->converter->value);
        $this->assertNull($obj->excluded);
        $this->assertSame('Exposed', $obj->exposed);
        $this->assertSame('AccessorValue', $obj->_getPublicProperty());
        $this->assertSame('multiple', $obj->_getMultiple()->value);
        $this->assertSame('Private', $obj->getPrivateProperty());
    }

    /**
     * @throws ExceptionInterface
     */
    public function testDenormalizeExclusionPolicyAll()
    {
        /** @var ExposeEntity $obj */
        $obj = $this->normalizer->denormalize($this->createData(), ExposeEntity::class);

        $this->assertInstanceOf(ExposeEntity::class, $obj);

        $this->assertNull($obj->foo);
        $this->assertNull($obj->simple);
        $this->assertNull($obj->converter);
        $this->assertNull($obj->excluded);
        $this->assertSame('Exposed', $obj->exposed);
        $this->assertNull($obj->_getPublicProperty());
        $this->assertNull($obj->_getMultiple());
        $this->assertSame('Private', $obj->getPrivateProperty());
    }

    /**
     * @throws ExceptionInterface
     */
    public function testDenormalizeWithGroups()
    {
        /** @var ExcludeEntity $obj */
        $obj = $this->normalizer->denormalize(
            $this->createData(),
            ExcludeEntity::class,
            null,
            ['groups' => ['group']]
        );

        $this->assertInstanceOf(ExcludeEntity::class, $obj);

        $this->assertNull($obj->foo);
        $this->assertNull($obj->simple);
        $this->assertNull($obj->converter);
        $this->assertNull($obj->excluded);
        $this->assertNull($obj->exposed);
        $this->assertSame('AccessorValue', $obj->_getPublicProperty());
        $this->assertNull($obj->_getMultiple());
        $this->assertSame('Private', $obj->getPrivateProperty());
    }

    /**
     * @throws ExceptionInterface
     */
    public function testDenormalizeWithAttributes()
    {
        /** @var ExcludeEntity $obj */
        $obj = $this->normalizer->denormalize(
            $this->createData(),
            ExcludeEntity::class,
            null,
            ['attributes' => ['foo', 'simple', 'multipleAnnotation', 'virtual']]
        );

        $this->assertInstanceOf(ExcludeEntity::class, $obj);

        $this->assertSame('Foo', $obj->foo);
        $this->assertSame('Simple', $obj->simple);
        $this->assertNull($obj->converter);
        $this->assertNull($obj->excluded);
        $this->assertNull($obj->exposed);
        $this->assertNull($obj->_getPublicProperty());
        $this->assertSame('multiple', $obj->_getMultiple()->value);
        $this->assertNull($obj->getPrivateProperty());
    }

    /**
     * @throws ExceptionInterface
     */
    public function testDenormalizeWithIgnoredAttributes()
    {
        /** @var ExcludeEntity $obj */
        $obj = $this->normalizer->denormalize(
            $this->createData(),
            ExcludeEntity::class,
            null,
            [
                'ignored_attributes' => [
                    'converter',
                    'excluded',
                    'exposed',
                    'accessor',
                    'multipleAnnotation',
                    'privateProperty',
                    'virtual'
                ],
            ]
        );

        $this->assertInstanceOf(ExcludeEntity::class, $obj);

        $this->assertSame('Foo', $obj->foo);
        $this->assertSame('Simple', $obj->simple);
        $this->assertNull($obj->converter);
        $this->assertNull($obj->excluded);
        $this->assertNull($obj->exposed);
        $this->assertNull($obj->_getPublicProperty());
        $this->assertNull($obj->_getMultiple());
        $this->assertNull($obj->getPrivateProperty());
    }

    public function testDenormalizeLogicExceptionSetterHasNotArguments()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Setter of attribute "foo" must have 1 argument.');

        /**
         * @ExtraSerialized()
         */
        $obj = new class {
            /**
             * @Accessor(setter="setFoo")
             */
            protected $foo;

            public function setFoo() {}
        };

        $this->normalizer->denormalize($this->createData(), get_class($obj));
    }

    public function testDenormalizeLogicExceptionSetterHas2Arguments()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Setter of attribute "foo" must have 1 argument.');

        /**
         * @ExtraSerialized()
         */
        $obj = new class {
            /**
             * @Accessor(setter="setFoo")
             */
            protected $foo;

            public function setFoo($foo, $bar) {
                $this->foo = $foo.$bar;
            }
        };

        $this->normalizer->denormalize($this->createData(), get_class($obj));
    }

    public function testDenormalizeLogicExceptionSetterNotPublic()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Setter of attribute "foo" is not public.');

        /**
         * @ExtraSerialized()
         */
        $obj = new class {
            /**
             * @Accessor(setter="setFoo")
             */
            protected $foo;

            protected function setFoo($foo) {
                $this->foo = $foo;
            }
        };

        $this->normalizer->denormalize($this->createData(), get_class($obj));
    }

    public function testDenormalizeLogicExceptionSetterIsStatic()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Setter of attribute "foo" is static.');

        /**
         * @ExtraSerialized()
         */
        $obj = new class {
            /**
             * @Accessor(setter="setFoo")
             */
            protected $foo;

            public static function setFoo($foo) {}
        };

        $this->normalizer->denormalize($this->createData(), get_class($obj));
    }

    protected function setUp(): void
    {
        $converter = new class implements ConverterInterface {

            public function getName(): string
            {
                return 'testConverter';
            }

            public function convert($object, string $attribute, $value, array $options)
            {
                return $value->value;
            }

            public function reconvert($object, string $attribute, $value, array $options)
            {
                return new AssociatedEntity($value);
            }
        };

        $annotationReader = new AnnotationReader();
        $metadataFactory = new \Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory(
            new \Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader($annotationReader)
        );
        $this->normalizer = new ExtraObjectNormalizer(
            new ClassMetadataFactory(new AnnotationLoader($annotationReader)),
            new ConverterContainer([$converter]),
            $metadataFactory,
            null,
            null
        );
    }

    private function createEntity(string $class)
    {
        /** @var ExcludeEntity|ExposeEntity $entity */
        $entity = new $class;
        $entity->foo = 'Foo';
        $entity->simple = 'Simple';
        $entity->converter = new AssociatedEntity('converter');
        $entity->excluded = 'Excluded';
        $entity->exposed = 'Exposed';
        $entity->_setPublicProperty('AccessorValue');
        $entity->_setMultiple(new AssociatedEntity('multiple'));
        $entity->setPrivateProperty('Private');

        return $entity;
    }

    private function createData(): array
    {
        return [
            'foo' => 'Foo',
            'simple' => 'Simple',
            'converter' => 'converter',
            'excluded' => 'Excluded',
            'exposed' => 'Exposed',
            'accessor' => 'AccessorValue',
            'multipleAnnotation' => 'multiple',
            'privateProperty' => 'Private'
        ];
    }
}
