<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Mapping\Loader;

use Doctrine\Common\Annotations\Reader;
use GetInfoTeam\SerializerExtraBundle\Annotation\Accessor;
use GetInfoTeam\SerializerExtraBundle\Annotation\Converter;
use GetInfoTeam\SerializerExtraBundle\Annotation\Exclude;
use GetInfoTeam\SerializerExtraBundle\Annotation\Expose;
use GetInfoTeam\SerializerExtraBundle\Annotation\ExtraSerialized;
use GetInfoTeam\SerializerExtraBundle\Annotation\VirtualAttribute;
use GetInfoTeam\SerializerExtraBundle\Exception\Mapping\NotExtraSerializedException;
use GetInfoTeam\SerializerExtraBundle\Mapping\AttributeMetadataBuilder;
use GetInfoTeam\SerializerExtraBundle\Mapping\AttributeMetadataBuilderInterface;
use GetInfoTeam\SerializerExtraBundle\Mapping\AttributeMetadataInterface;
use GetInfoTeam\SerializerExtraBundle\Mapping\ClassMetadataBuilder;
use GetInfoTeam\SerializerExtraBundle\Mapping\ClassMetadataInterface;
use GetInfoTeam\SerializerExtraBundle\Mapping\Factory\ClassResolverTrait;
use ReflectionClass;
use ReflectionException;

class AnnotationLoader implements LoaderInterface
{
    use ClassResolverTrait;

    /** @var Reader */
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param string $class
     * @return ClassMetadataInterface
     * @throws ReflectionException
     */
    public function load(string $class): ClassMetadataInterface
    {
        $class = $this->getClass($class);

        $classRef = new ReflectionClass($class);
        $classMetadataBuilder = ClassMetadataBuilder::create($class);

        /** @var ExtraSerialized|null $extraSerialized */
        $extraSerialized = $this->reader->getClassAnnotation($classRef, ExtraSerialized::class);

        if (is_null($extraSerialized)) {
            throw new NotExtraSerializedException($class);
        }

        $classMetadataBuilder->setExclusionPolicy($extraSerialized->policy);

        foreach ($extraSerialized->properties as $property) {
            $classMetadataBuilder->addAttribute($this->resolveVirtualAttributeAnnotation($property));
        }

        foreach ($classRef->getProperties() as $property) {
            $attributeMetadataBuilder = AttributeMetadataBuilder::create($property->getName());
            $this->resolveAttributeAnnotations(
                $this->reader->getPropertyAnnotations($property),
                $attributeMetadataBuilder
            );
            $classMetadataBuilder->addAttribute($attributeMetadataBuilder->build());
        }

        return $classMetadataBuilder->build();
    }

    protected function resolveVirtualAttributeAnnotation(VirtualAttribute $attribute): AttributeMetadataInterface
    {
        return AttributeMetadataBuilder::create($attribute->name)
            ->setExclude($attribute->exclude)
            ->setExpose($attribute->expose)
            ->setGetter($attribute->getter)
            ->setSetter($attribute->setter)
            ->setConverter($attribute->converter)
            ->setOptions($attribute->options)
            ->build()
        ;
    }

    protected function resolveAttributeAnnotations(array $annotations, AttributeMetadataBuilderInterface $builder): void
    {
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Exclude) {
                $builder->setExclude(true);
            } elseif ($annotation instanceof Expose) {
                $builder->setExpose(true);
            } elseif ($annotation instanceof Accessor) {
                $builder->setGetter($annotation->getter);
                $builder->setSetter($annotation->setter);
            } elseif ($annotation instanceof Converter) {
                $builder->setConverter($annotation->converter);
                $builder->setOptions($annotation->options);
            }
        }
    }
}