<?php

namespace Kunstmaan\RedirectBundle\Tests\DependencyInjection;

use Kunstmaan\RedirectBundle\DependencyInjection\KunstmaanRedirectExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * Class KunstmaanRedirectExtensionTest
 */
class KunstmaanRedirectExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [new KunstmaanRedirectExtension()];
    }

    public function testCorrectParametersHaveBeenSet()
    {
        $this->container->setParameter('empty_extension', true);
        $this->load();

        $this->assertContainerBuilderHasParameter('empty_extension', true);
    }
}
