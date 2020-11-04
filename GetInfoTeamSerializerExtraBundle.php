<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle;

use GetInfoTeam\SerializerExtraBundle\DependencyInjection\Compiler\ConverterPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GetInfoTeamSerializerExtraBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConverterPass());
    }
}