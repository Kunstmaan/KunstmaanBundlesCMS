<?php

namespace Kunstmaan\MultiDomainBundle\Tests\DependencyInjection;

use Kunstmaan\MultiDomainBundle\DependencyInjection\KunstmaanMultiDomainExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Kunstmaan\AdminBundle\Tests\unit\AbstractPrependableExtensionTestCase;

/**
 * Class KunstmaanMultiDomainExtensionTest
 */
class KunstmaanMultiDomainExtensionTest extends AbstractPrependableExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [new KunstmaanMultiDomainExtension()];
    }

    public function testCorrectParametersHaveBeenSet()
    {
        $this->load([
            'hosts' => [
                [
                    'host' => 'cia.gov',
                    'protocol' => 'https',
                    'aliases' => ['cia.com'],
                    'type' => 'single_lang',
                    'root' => 'homepage',
                    'default_locale' => 'nl',
                    'locales' => [
                        [
                            'uri_locale' => '/nl',
                            'locale' => 'nl',
                            'extra' => 'huh?',
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter('kunstmaan_multi_domain.hosts');
        $this->assertContainerBuilderHasParameter('kunstmaan_multi_domain.router.class', 'Kunstmaan\MultiDomainBundle\Router\DomainBasedLocaleRouter');
        $this->assertContainerBuilderHasParameter('kunstmaan_multi_domain.domain_configuration.class', 'Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration');
        $this->assertContainerBuilderHasParameter('kunstmaan_node.slugrouter.class', 'Kunstmaan\MultiDomainBundle\Router\DomainBasedLocaleRouter');

        $this->assertContainerBuilderHasAlias('kunstmaan_admin.domain_configuration', 'kunstmaan_multi_domain.domain_configuration');
    }
}
