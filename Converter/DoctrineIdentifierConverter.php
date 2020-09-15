<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Converter;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use GetInfoTeam\SerializerExtraBundle\Exception\Converter\CollectionTypeValueNotIterableException;
use GetInfoTeam\SerializerExtraBundle\Exception\Converter\EntityNotFoundException;
use GetInfoTeam\SerializerExtraBundle\Exception\Converter\MultiColumnIdentifierException;
use GetInfoTeam\SerializerExtraBundle\Exception\Converter\NoIdentifierException;
use GetInfoTeam\SerializerExtraBundle\Exception\Converter\ResolveAttributeTypeException;
use GetInfoTeam\SerializerExtraBundle\Mapping\Factory\ClassResolverTrait;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

if (
    interface_exists(ObjectManager::class) &&
    interface_exists(PropertyTypeExtractorInterface::class)
) {
    class DoctrineIdentifierConverter implements ConverterInterface
    {
        use ClassResolverTrait;

        const DEFAULT_NAME = 'extra_serializer.doctrine.id';

        const DEFAULT_ERROR_MESSAGE = 'Not found.';

        const DEFAULT_COLLECTION_ERROR_MESSAGE = 'Entities with identifiers %s not found.';

        const OPTION_ERROR_MESSAGE = 'error_message';

        /** @var string */
        private $name;

        /** @var ObjectManager */
        private $manager;

        /** @var PropertyTypeExtractorInterface */
        private $propertyTypeExtractor;

        public function __construct(
            ObjectManager $manager,
            PropertyTypeExtractorInterface $propertyTypeExtractor,
            string $name = self::DEFAULT_NAME
        ) {
            $this->manager = $manager;
            $this->propertyTypeExtractor = $propertyTypeExtractor;
            $this->name = $name;
        }

        public function getName(): string
        {
            return $this->name;
        }

        public function convert($object, string $attribute, $value, array $options)
        {
            $class = $this->getClass($object);
            $type = $this->getType($class, $attribute);

            $metadata = $this->getClassMetadata($type);

            if ($type->isCollection()) {
                if (!is_iterable($value)) {
                    throw new CollectionTypeValueNotIterableException();
                }

                return array_map(
                    function ($value) use ($metadata) {
                        return $this->getIdentifierValue($metadata, $value);
                    },
                    $value
                );
            } else {
                return $this->getIdentifierValue($metadata, $value);
            }
        }

        public function reconvert($object, string $attribute, $value, array $options)
        {
            $class = $this->getClass($object);
            $type = $this->getType($class, $attribute);

            $repository = $this->getRepository($type);

            if ($type->isCollection()) {
                if (!is_iterable($value)) {
                    throw new CollectionTypeValueNotIterableException();
                }

                $metadata = $this->getClassMetadata($type);
                $idField = $this->getIdentifierField($metadata);

                $result = array_values($repository->findBy([$idField => $value]));

                if (count($value) !== count($result)) {
                    $notFoundIds = array_diff(
                        $value,
                        array_map(
                            function($r) use ($metadata) {
                                return $this->getIdentifierValue($metadata, $r);
                            },
                            $result
                        )
                    );

                    $errorMessage = static::getCollectionErrorMessage(
                        $notFoundIds,
                        $options[static::OPTION_ERROR_MESSAGE] ?? static::DEFAULT_COLLECTION_ERROR_MESSAGE
                    );

                    throw new EntityNotFoundException($errorMessage);
                }

                return $result;
            } else {
                $result = $repository->find($value);

                if (is_null($result)) {
                    $errorMessage = $options[static::OPTION_ERROR_MESSAGE] ?? static::DEFAULT_ERROR_MESSAGE;
                    throw new EntityNotFoundException($errorMessage);
                }

                return $result;
            }
        }

        private function getType(string $class, string $attribute): Type
        {
            $types = $this->propertyTypeExtractor->getTypes($class, $attribute);

            if (!$types || count($types) > 0) {
                throw new ResolveAttributeTypeException($class, $attribute);
            }

            return $types[0];
        }

        private function getClassMetadata(Type $type): ClassMetadata
        {
            return $this->manager->getClassMetadata($this->getTypeClassName($type));
        }

        private function getRepository(Type $type): ObjectRepository
        {
            return $this->manager->getRepository($this->getTypeClassName($type));
        }

        /**
         * @param ClassMetadata $metadata
         * @return string
         */
        private function getIdentifierField(ClassMetadata $metadata): string
        {
            $fields = $metadata->getIdentifierFieldNames();

            if (empty($fields)) {
                throw new NoIdentifierException($metadata->getName());
            }

            if (count($fields) > 1) {
                throw new MultiColumnIdentifierException($metadata->getName());
            }

            return $fields[0];
        }

        /**
         * @param ClassMetadata $metadata
         * @param object $object
         * @return mixed
         */
        private function getIdentifierValue(ClassMetadata $metadata, $object)
        {
            $values = $metadata->getIdentifierValues($object);

            if (empty($values)) {
                return null;
            }

            if (count($values) > 1) {
                throw new MultiColumnIdentifierException($metadata->getName());
            }

            return $values[0];
        }

        private function getTypeClassName(Type $type): string
        {
            return $type->isCollection() ? $type->getCollectionValueType()->getClassName() : $type->getClassName();
        }

        private static function getCollectionErrorMessage(array $notFoundIds, string $template): string
        {
            return sprintf(
                $template,
                implode(
                    ", ",
                    array_map(
                        function ($id) {
                            return '"'.$id.'"';
                        },
                        $notFoundIds
                    )
                )
            );
        }
    }
}