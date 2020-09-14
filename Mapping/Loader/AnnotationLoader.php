<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Mapping\Loader;

use Doctrine\Common\Annotations\Reader;
use GetInfoTeam\SerializerExtraBundle\Annotation\Accessor;
use GetInfoTeam\SerializerExtraBundle\Annotation\Converter;
use GetInfoTeam\SerializerExtraBundle\Annotation\Exclude;
use GetInfoTeam\SerializerExtraBundle\Annotation\ExclusionPolicy;
use GetInfoTeam\SerializerExtraBundle\Annotation\Expose;
use GetInfoTeam\SerializerExtraBundle\Annotation\VirtualAttribute;
use GetInfoTeam\SerializerExtraBundle\Mapping\AttributeMetadata;
use GetInfoTeam\SerializerExtraBundle\Mapping\AttributeMetadataBuilder;
use GetInfoTeam\SerializerExtraBundle\Mapping\AttributeMetadataBuilderInterface;
use GetInfoTeam\SerializerExtraBundle\Mapping\ClassMetadataBuilder;
use GetInfoTeam\SerializerExtraBundle\Mapping\ClassMetadataBuilderInterface;
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

        $this->resolveClassAnnotations($this->reader->getClassAnnotations($classRef), $classMetadataBuilder);

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

    protected function resolveClassAnnotations(array $annotations, ClassMetadataBuilderInterface $builder): void
    {
        foreach ($annotations as $annotation) {
            if ($annotation instanceof ExclusionPolicy) {
                $builder->setExclusionPolicy($annotation->policy);
            } elseif ($annotation instanceof VirtualAttribute) {
                $builder->addAttribute(
                    new AttributeMetadata(
                        $annotation->name,
                        $annotation->exclude,
                        $annotation->expose,
                        $annotation->getter,
                        $annotation->setter,
                        $annotation->converter,
                        $annotation->options
                    )
                );
            }
        }
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