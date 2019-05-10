<?php

namespace Kunstmaan\CacheBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
final class DeprecateClassParametersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $expectedValues = [
            'kunstmaan_cache.menu_adaptor.varnish.class' => \Kunstmaan\CacheBundle\Helper\Menu\VarnishMenuAdaptor::class,
            'kunstmaan_cache.helper.varnish.class' => \Kunstmaan\CacheBundle\Helper\VarnishHelper::class,
        ];

        foreach ($expectedValues as $parameter => $expectedValue) {
            if (false === $container->hasParameter($parameter)) {
                continue;
            }

            $currentValue = $container->getParameter($parameter);
            if ($currentValue !== $expectedValue) {
                @trigger_error(sprintf('Using the "%s" parameter to change the class of the service definition is deprecated in KunstmaanCacheBundle 5.2 and will be removed in KunstmaanCacheBundle 6.0. Use service decoration or a service alias instead.', $parameter), E_USER_DEPRECATED);
            }
        }
    }
}
