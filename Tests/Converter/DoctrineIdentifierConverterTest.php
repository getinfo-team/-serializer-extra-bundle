<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Tests\Converter;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use GetInfoTeam\SerializerExtraBundle\Converter\DoctrineIdentifierConverter;
use GetInfoTeam\SerializerExtraBundle\Exception\Converter\CollectionTypeValueNotIterableException;
use GetInfoTeam\SerializerExtraBundle\Exception\Converter\EntityNotFoundException;
use GetInfoTeam\SerializerExtraBundle\Exception\Converter\MultiColumnIdentifierException;
use GetInfoTeam\SerializerExtraBundle\Exception\Converter\NoIdentifierException;
use GetInfoTeam\SerializerExtraBundle\Exception\Converter\ResolveAttributeTypeException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;

if (
    interface_exists(ObjectManager::class) &&
    interface_exists(PropertyTypeExtractorInterface::class)
) {
    class DoctrineIdentifierConverterTest extends TestCase
    {
        /** @var ObjectManager|MockObject */
        private $om;

        private $converter;

        public function testGetName()
        {
            $this->assertSame('extra_serializer.doctrine.id', $this->converter->getName());
        }

        public function testConvert()
        {
            $this->om->expects($this->any())->method('getClassMetadata')->willReturn($this->createClassMetadata());

            $entity = new DoctrineEntity();
            $entity->association = new DoctrineAssociatedEntity(1);

            $this->assertSame(1, $this->converter->convert($entity, 'association', $entity->association, []));
        }

        public function testConvertNull()
        {
            $entity = new DoctrineEntity();

            $this->assertNull($this->converter->convert($entity, 'association', $entity->association, []));
        }

        public function testConvertIdentifierValueEmpty()
        {
            $this->om->expects($this->any())
                ->method('getClassMetadata')
                ->willReturn($this->createClassMetadata([], []))
            ;

            $entity = new DoctrineEntity();
            $entity->association = new DoctrineAssociatedEntity(1);

            $this->assertNull($this->converter->convert($entity, 'association', $entity->association, []));
        }

        public function testConvertIdentifierValueMultiple()
        {
            $this->expectException(MultiColumnIdentifierException::class);
            $this->expectExceptionMessage(
                sprintf('Class "%s" has multi-column identifier.', DoctrineAssociatedEntity::class)
            );

            $this->om->expects($this->any())
                ->method('getClassMetadata')
                ->willReturn($this->createClassMetadata([], ['foo', 'bar']))
            ;

            $entity = new DoctrineEntity();
            $entity->association = new DoctrineAssociatedEntity(1);

            $this->assertNull($this->converter->convert($entity, 'association', $entity->association, []));
        }

        public function testConvertCollection()
        {
            $this->om->expects($this->any())->method('getClassMetadata')->willReturn($this->createClassMetadata());

            $entity = new DoctrineEntity();
            $entity->collectionAssociations[] = new DoctrineAssociatedEntity(1);
            $entity->collectionAssociations[] = new DoctrineAssociatedEntity(2);
            $entity->collectionAssociations[] = new DoctrineAssociatedEntity(3);

            $this->assertSame(
                [1, 2, 3],
                $this->converter->convert($entity, 'collectionAssociations', $entity->collectionAssociations, [])
            );
        }

        public function testConvertTypeNotResolve()
        {
            $this->expectException(ResolveAttributeTypeException::class);
            $this->expectExceptionMessage(
                sprintf(ResolveAttributeTypeException::MESSAGE, DoctrineEntity::class, 'typeNotResolve')
            );

            $this->converter->convert(new DoctrineEntity(), 'typeNotResolve', 'Value', []);
        }

        public function testConvertMultipleTypes()
        {
            $this->expectException(ResolveAttributeTypeException::class);
            $this->expectExceptionMessage(
                sprintf(ResolveAttributeTypeException::MESSAGE, DoctrineEntity::class, 'multipleTypes')
            );

            $this->converter->convert(new DoctrineEntity(), 'multipleTypes', 'Value', []);
        }

        public function testConvertCollectionTypeValueNotIterableException()
        {
            $this->expectException(CollectionTypeValueNotIterableException::class);

            $this->om->expects($this->any())->method('getClassMetadata')->willReturn($this->createClassMetadata());

            $this->converter->convert(new DoctrineEntity(), 'collectionAssociations', 'notIterable', []);
        }

        public function testReconvert()
        {
            $association = new DoctrineAssociatedEntity(1);
            $entity = new DoctrineEntity();

            $this->om
                ->expects($this->any())
                ->method('getRepository')
                ->willReturn($this->createRepository($association, false))
            ;

            $this->assertSame($association, $this->converter->reconvert($entity, 'association', 1, []));
        }

        public function testReconvertNull()
        {
            $entity = new DoctrineEntity();

            $this->assertNull($this->converter->reconvert($entity, 'association', null, []));
        }

        public function testReconvertNotFoundException()
        {
            $this->expectException(EntityNotFoundException::class);
            $this->expectExceptionMessage('Not found.');

            $this->om
                ->expects($this->any())
                ->method('getRepository')
                ->willReturn($this->createRepository(null, false))
            ;

            $entity = new DoctrineEntity();

            $this->converter->reconvert($entity, 'association', 1, ['error_message' => 'Not found.']);
        }

        public function testReconvertCollection()
        {
            $associations = [
                new DoctrineAssociatedEntity(3),
                new DoctrineAssociatedEntity(2),
                new DoctrineAssociatedEntity(1),
            ];
            $entity = new DoctrineEntity();

            $this->om->expects($this->any())->method('getClassMetadata')->willReturn($this->createClassMetadata());
            $this->om
                ->expects($this->any())
                ->method('getRepository')
                ->willReturn($this->createRepository($associations, true))
            ;

            $this->assertSame(
                $associations,
                $this->converter->reconvert($entity, 'collectionAssociations', [3, 2, 1], [])
            );
        }

        public function testReconvertCollectionTypeValueNotIterableException()
        {
            $this->expectException(CollectionTypeValueNotIterableException::class);

            $this->converter->reconvert(new DoctrineEntity(), 'collectionAssociations', 'notIterable', []);
        }

        public function testReconvertCollectionNoIdentifierException()
        {
            $this->expectException(NoIdentifierException::class);
            $this->expectExceptionMessage(sprintf(NoIdentifierException::MESSAGE, DoctrineAssociatedEntity::class));

            $this->om->expects($this->any())->method('getClassMetadata')->willReturn($this->createClassMetadata([]));

            $this->converter->reconvert(new DoctrineEntity(), 'collectionAssociations', [1], []);
        }

        public function testReconvertMultiColumnIdentifierException()
        {
            $this->expectException(MultiColumnIdentifierException::class);
            $this->expectExceptionMessage(sprintf(MultiColumnIdentifierException::MESSAGE, DoctrineAssociatedEntity::class));

            $this->om->expects($this->any())->method('getClassMetadata')->willReturn($this->createClassMetadata(['id1', 'id2']));

            $this->converter->reconvert(new DoctrineEntity(), 'collectionAssociations', [1], []);
        }

        public function testReconvertCollectionNotFoundException()
        {
            $this->expectException(EntityNotFoundException::class);
            $this->expectExceptionMessage('Entities with identifiers "2", "3" not found.');

            $this->om->expects($this->any())->method('getClassMetadata')->willReturn($this->createClassMetadata());
            $this->om
                ->expects($this->any())
                ->method('getRepository')
                ->willReturn($this->createRepository([new DoctrineAssociatedEntity(1)], true))
            ;

            $this->converter->reconvert(new DoctrineEntity(), 'collectionAssociations', [1,2,3], []);
        }

        protected function setUp(): void
        {
            /** @var ObjectManager|MockObject $om */
            $om = $this->getMockBuilder(ObjectManager::class)
                ->onlyMethods(['getClassMetadata', 'getRepository'])
                ->getMockForAbstractClass()
            ;
            $this->om = $om;

            $reflectionExtractor = new ReflectionExtractor();
            $phpDocExtractor = new PhpDocExtractor();

            $extractor = new PropertyInfoExtractor(
                [$reflectionExtractor],
                [$phpDocExtractor, $reflectionExtractor],
                [$phpDocExtractor],
                [$reflectionExtractor],
                [$reflectionExtractor]
            );

            $this->converter = new DoctrineIdentifierConverter($om, $extractor);
        }

        private function createClassMetadata(?array $ids = null, ?array $values = null): ClassMetadata
        {
            /** @var ClassMetadata|MockObject $metadata */
            $metadata = $this->getMockBuilder(ClassMetadata::class)
                ->onlyMethods(['getName', 'getIdentifierFieldNames', 'getIdentifierValues'])
                ->getMockForAbstractClass()
            ;
            $metadata->expects($this->any())->method('getName')->willReturn(DoctrineAssociatedEntity::class);
            $metadata->expects($this->any())->method('getIdentifierFieldNames')->willReturn($ids ?? ['id']);

            if (is_array($values)) {
                $metadata->expects($this->any())->method('getIdentifierValues')->willReturn($values);
            } else {
                $metadata->expects($this->any())
                    ->method('getIdentifierValues')
                    ->willReturnCallback(
                        function (DoctrineAssociatedEntity $entity) {
                            return [$entity->id];
                        }
                    );
            }

            return $metadata;
        }

        private function createRepository($value, bool $isCollection): ObjectRepository
        {
            /** @var ObjectRepository|MockObject $repository */
            $repository = $this->getMockBuilder(ObjectRepository::class)
                ->onlyMethods(['find', 'findBy'])
                ->getMockForAbstractClass()
            ;
            $repository->expects($this->any())->method($isCollection ? 'findBy' : 'find')->willReturn($value);

            return $repository;
        }
    }
}