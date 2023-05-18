<?php

namespace Kunstmaan\RedirectBundle\Tests\DependencyInjection;

use Kunstmaan\RedirectBundle\DependencyInjection\KunstmaanRedirectExtension;
use Kunstmaan\RedirectBundle\Entity\Redirect;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class KunstmaanRedirectExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions(): array
    {
        return [new KunstmaanRedirectExtension()];
    }

    public function testRedirectClassParameterWithConfigValue()
    {
        $this->load([
            'redirect_entity' => 'RedirectTestEntity',
            'enable_improved_router' => true,
        ]);

        $this->assertContainerBuilderHasParameter('kunstmaan_redirect.redirect.class', 'RedirectTestEntity');
    }

    public function testDefaultRedirectClassParameter()
    {
        $this->load(['enable_improved_router' => true]);

        $this->assertContainerBuilderHasParameter('kunstmaan_redirect.redirect.class', Redirect::class);
    }
}
