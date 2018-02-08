<?php

namespace Kunstmaan\TranslatorBundle;

use Kunstmaan\TranslatorBundle\DependencyInjection\Compiler\DeprecationsCompilerPass;
use Kunstmaan\TranslatorBundle\DependencyInjection\Compiler\KunstmaanTranslatorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class KunstmaanTranslatorBundle
 *
 * @package Kunstmaan\TranslatorBundle
 */
class KunstmaanTranslatorBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new KunstmaanTranslatorCompilerPass());
        $container->addCompilerPass(new DeprecationsCompilerPass());
    }
}
