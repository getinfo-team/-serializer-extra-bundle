<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Normalizer;

use GetInfoTeam\SerializerExtraBundle\Converter\ConverterContainerInterface;
use GetInfoTeam\SerializerExtraBundle\Exception\LogicException;
use GetInfoTeam\SerializerExtraBundle\Exception\Mapping\NotExtraSerializedException;
use GetInfoTeam\SerializerExtraBundle\Mapping\AttributeMetadataInterface;
use GetInfoTeam\SerializerExtraBundle\Mapping\ClassMetadataInterface;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use GetInfoTeam\SerializerExtraBundle\Mapping\Factory\ClassMetadataFactoryInterface as ExtraClassMetadataFactoryInterface;

class ExtraObjectNormalizer extends AbstractObjectNormalizer
{
    /** @var ExtraClassMetadataFactoryInterface */
    private $extraClassMetadataFactory;

    /** @var ConverterContainerInterface */
    private $converterContainer;

    /** @var PropertyAccessorInterface */
    private $accessor;

    public function __construct(
        ExtraClassMetadataFactoryInterface $extraClassMetadataFactory,
        ConverterContainerInterface $converterContainer,
        ClassMetadataFactoryInterface $classMetadataFactory = null,
        PropertyAccessorInterface $accessor = null,
        NameConverterInterface $nameConverter = null,
        PropertyTypeExtractorInterface $propertyTypeExtractor = null,
        ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null,
        callable $objectClassResolver = null,
        array $defaultContext = []
    ) {
        parent::__construct(
            $classMetadataFactory,
            $nameConverter,
            $propertyTypeExtractor,
            $classDiscriminatorResolver,
            $objectClassResolver,
            $defaultContext
        );

        $this->extraClassMetadataFactory = $extraClassMetadataFactory;
        $this->converterContainer = $converterContainer;
        $this->accessor = $accessor ?: PropertyAccess::createPropertyAccessor();
    }

    public function supportsNormalization($data, $format = null)
    {
        if (!parent::supportsNormalization($data, $format)) {
            return false;
        }

        try {
            $this->extraClassMetadataFactory->getMetadataFor($data);

            return true;
        } catch (NotExtraSerializedException $e) {
            return false;
        }
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        if (!parent::supportsDenormalization($data, $type, $format)) {
            return false;
        }

        try {
            $this->extraClassMetadataFactory->getMetadataFor($type);

            return true;
        } catch (NotExtraSerializedException $e) {
            return false;
        }
    }

    protected function extractAttributes($object, $format = null, array $context = [])
    {
        return array_map(
            function(AttributeMetadataInterface $attr) {
                return $attr->getName();
            },
            array_filter(
                $this->extraClassMetadataFactory->getMetadataFor($object)->getAttributes(),
                function(AttributeMetadataInterface $attr) use ($object) {
                    return $this->accessor->isReadable($object, $attr->getName());
                }
            )
        );
    }

    protected function isAllowedAttribute($classOrObject, $attribute, $format = null, array $context = [])
    {
        $metadata = $this->extraClassMetadataFactory->getMetadataFor($classOrObject);

        switch ($metadata->getExclusionPolicy()) {
            case ClassMetadataInterface::EXCLUSION_POLICY_ALL:
                return $metadata->getAttribute($attribute)->isExpose();

            case ClassMetadataInterface::EXCLUSION_POLICY_NONE:
                return !$metadata->getAttribute($attribute)->isExclude();
        }

        return parent::isAllowedAttribute(
            $classOrObject,
            $attribute,
            $format,
            $context
        );
    }

    /**
     * @param object $object
     * @param string $attribute
     * @param null $format
     * @param array $context
     * @return mixed|null
     * @throws ReflectionException
     */
    protected function getAttributeValue($object, $attribute, $format = null, array $context = [])
    {
        $metadata = $this->extraClassMetadataFactory->getMetadataFor($object)->getAttribute($attribute);

        if ($metadata->getter()) {
            $getter = new ReflectionMethod($object, $metadata->getter());

            if (0 !== $getter->getNumberOfRequiredParameters()) {
                throw new LogicException(sprintf('Getter of attribute "%s" has required parameters.', $attribute));
            }

            if (!$getter->isPublic()) {
                throw new LogicException(sprintf('Getter of attribute "%s" is not public.', $attribute));
            }

            if ($getter->isStatic()) {
                throw new LogicException(sprintf('Getter of attribute "%s" is static.', $attribute));
            }

            if ($getter->isConstructor()) {
                throw new LogicException(sprintf('Getter of attribute "%s" is constructor.', $attribute));
            }

            if ($getter->isDestructor()) {
                throw new LogicException(sprintf('Getter of attribute "%s" is destructor.', $attribute));
            }

            $value = $getter->invoke($object);
        } else {
            $value = $this->accessor->getValue($object, $attribute);
        }

        if ($metadata->getConverter()) {
            $converter = $this->converterContainer->get($metadata->getConverter());
            $value = $converter->convert($object, $attribute, $value, $metadata->getOptions());
        }

        return $value;
    }

    /**
     * @param object $object
     * @param string $attribute
     * @param mixed $value
     * @param null $format
     * @param array $context
     * @throws ReflectionException
     */
    protected function setAttributeValue($object, $attribute, $value, $format = null, array $context = [])
    {
        $metadata = $this->extraClassMetadataFactory->getMetadataFor($object)->getAttribute($attribute);

        if ($metadata->getConverter()) {
            $converter = $this->converterContainer->get($metadata->getConverter());
            $value = $converter->reconvert($object, $attribute, $value, $metadata->getOptions());
        }

        if ($metadata->setter()) {
            $setter = new ReflectionMethod($object, $metadata->setter());

            if (1 !== $setter->getNumberOfParameters()) {
                throw new LogicException(sprintf('Setter of attribute "%s" must have 1 parameter.', $attribute));
            }

            if (!$setter->isPublic()) {
                throw new LogicException(sprintf('Setter of attribute "%s" is not public.', $attribute));
            }

            if ($setter->isStatic()) {
                throw new LogicException(sprintf('Setter of attribute "%s" is static.', $attribute));
            }

            if ($setter->isConstructor()) {
                throw new LogicException(sprintf('Setter of attribute "%s" is constructor.', $attribute));
            }

            if ($setter->isDestructor()) {
                throw new LogicException(sprintf('Setter of attribute "%s" is destructor.', $attribute));
            }

            $setter->invoke($object, $value);
        } else {
            $this->accessor->setValue($object, $attribute, $value);
        }
    }
}