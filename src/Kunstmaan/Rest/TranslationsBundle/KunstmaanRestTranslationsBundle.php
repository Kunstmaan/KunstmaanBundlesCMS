<?php

namespace Kunstmaan\Rest\TranslationsBundle;

use Kunstmaan\Rest\TranslationsBundle\DependencyInjection\Compiler\NelmioDefinitionsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class KunstmaanRestTranslationsBundle
 */
class KunstmaanRestTranslationsBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new NelmioDefinitionsCompilerPass());
    }
}
