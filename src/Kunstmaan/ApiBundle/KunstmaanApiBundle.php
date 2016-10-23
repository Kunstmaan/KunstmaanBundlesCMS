<?php

namespace Kunstmaan\ApiBundle;

use Kunstmaan\ApiBundle\DependencyInjection\Compiler\TransformerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanApiBundle extends Bundle
{
    public function build(ContainerBuilder $builder)
    {
        $builder->addCompilerPass(new TransformerCompilerPass());
    }
}
