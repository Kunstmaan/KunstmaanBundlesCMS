<?php

namespace Kunstmaan\RedirectBundle\Tests\DependencyInjection;

use Kunstmaan\RedirectBundle\DependencyInjection\KunstmaanRedirectExtension;
use Kunstmaan\RedirectBundle\Entity\Redirect;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class KunstmaanRedirectExtensionTest extends AbstractExtensionTestCase
{
    use ExpectDeprecationTrait;

    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions(): array
    {
        return [new KunstmaanRedirectExtension()];
    }

    public function testRedirectClassParameterWithConfigValue()
    {
        $this->load(['redirect_entity' => 'RedirectTestEntity']);

        $this->assertContainerBuilderHasParameter('kunstmaan_redirect.redirect.class', 'RedirectTestEntity');
    }

    public function testDefaultRedirectClassParameter()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_redirect.redirect.class', Redirect::class);
    }
}
