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
        $classBuilder = ClassMetadataBuilder::create($class);

        /** @var ExtraSerialized|null $extraSerialized */
        $extraSerialized = $this->reader->getClassAnnotation($classRef, ExtraSerialized::class);

        if (is_null($extraSerialized)) {
            throw new NotExtraSerializedException($class);
        }

        $classBuilder->setExclusionPolicy($extraSerialized->policy);

        /** @var AttributeMetadataBuilderInterface[] $attributeBuilders */
        $attributeBuilders = [];

        foreach ($classRef->getProperties() as $property) {
            $builder = new AttributeMetadataBuilder($property->getName());
            $this->resolveAttributeAnnotations($builder, $this->reader->getPropertyAnnotations($property));
            $attributeBuilders[$property->getName()] = $builder;
        }

        /** @var VirtualAttribute $property */
        foreach ($extraSerialized->properties as $property) {
            if (!isset($attributeBuilders[$property->name])) {
                $attributeBuilders[$property->name] = new AttributeMetadataBuilder($property->name);
            }

            $this->resolveVirtualAttributeAnnotation($attributeBuilders[$property->name], $property);
        }

        foreach ($attributeBuilders as $builder) {
            $classBuilder->addAttribute($builder->build());
        }

        return $classBuilder->build();
    }

    protected function resolveAttributeAnnotations(AttributeMetadataBuilderInterface $builder, array $annotations): void
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

    protected function resolveVirtualAttributeAnnotation(
        AttributeMetadataBuilderInterface $builder,
        VirtualAttribute $attribute
    ): void
    {
        if (is_bool($attribute->exclude)) {
            $builder->setExclude($attribute->exclude);
        }

        if (is_bool($attribute->expose)) {
            $builder->setExpose($attribute->expose);
        }

        if (is_string($attribute->getter)) {
            $builder->setGetter($attribute->getter);
        }

        if (is_string($attribute->setter)) {
            $builder->setSetter($attribute->setter);
        }

        if (is_string($attribute->converter)) {
            $builder->setConverter($attribute->converter);
        }

        if (is_array($attribute->options)) {
            $builder->setOptions($attribute->options);
        }
    }
}