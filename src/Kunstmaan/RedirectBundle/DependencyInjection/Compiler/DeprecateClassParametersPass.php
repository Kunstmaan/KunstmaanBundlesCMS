<?php

namespace Kunstmaan\RedirectBundle\DependencyInjection\Compiler;

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
            'kunstmaan_redirect.menu.adaptor.class' => \Kunstmaan\RedirectBundle\Helper\Menu\RedirectMenuAdaptor::class,
            'kunstmaan_redirect.redirect_repository.class' => \Kunstmaan\RedirectBundle\Repository\RedirectRepository::class,
        ];

        foreach ($expectedValues as $parameter => $expectedValue) {
            if (false === $container->hasParameter($parameter)) {
                continue;
            }

            $currentValue = $container->getParameter($parameter);
            if ($currentValue !== $expectedValue) {
                @trigger_error(sprintf('Using the "%s" parameter to change the class of the service definition is deprecated in KunstmaanRedirectBundle 5.2 and will be removed in KunstmaanRedirectBundle 6.0. Use service decoration or a service alias instead.', $parameter), E_USER_DEPRECATED);
            }
        }
    }
}
