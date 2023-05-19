<?php

namespace Kunstmaan\TranslatorBundle;

use Kunstmaan\TranslatorBundle\DependencyInjection\Compiler\KunstmaanTranslatorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanTranslatorBundle extends Bundle
{
    /**
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new KunstmaanTranslatorCompilerPass());
    }
}
