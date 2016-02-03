<?php

namespace Kunstmaan\FixturesBundle;

use Kunstmaan\FixturesBundle\DependencyInjection\Compiler\BuilderCompilerPass;
use Kunstmaan\FixturesBundle\DependencyInjection\Compiler\ParserCompilerPass;
use Kunstmaan\FixturesBundle\DependencyInjection\Compiler\PopulatorCompilerPass;
use Kunstmaan\FixturesBundle\DependencyInjection\Compiler\ProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanFixturesBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new BuilderCompilerPass());
        $container->addCompilerPass(new PopulatorCompilerPass());
        $container->addCompilerPass(new ParserCompilerPass());
        $container->addCompilerPass(new ProviderCompilerPass());
    }
}
