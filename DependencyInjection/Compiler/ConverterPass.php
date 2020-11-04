<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\DependencyInjection\Compiler;

use GetInfoTeam\SerializerExtraBundle\Converter\DoctrineIdentifierConverter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ConverterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasExtension('doctrine')) {
            $this->addDoctrineIdentifierConverter($container);
        }
    }

    private function addDoctrineIdentifierConverter(ContainerBuilder $container): void
    {
        $definition = new Definition(
            DoctrineIdentifierConverter::class,
            [
                new Reference('doctrine.orm.entity_manager'),
                new Reference('property_info')
            ]
        );
        $definition->addTag('serializer_extra.converter');

        $container->setDefinition(
            DoctrineIdentifierConverter::class,
            $definition
        );
    }
}