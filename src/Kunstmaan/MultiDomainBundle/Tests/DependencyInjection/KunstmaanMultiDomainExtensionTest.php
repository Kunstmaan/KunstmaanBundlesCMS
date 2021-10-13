<?php

namespace Kunstmaan\MultiDomainBundle\Tests\DependencyInjection;

use Kunstmaan\MultiDomainBundle\DependencyInjection\KunstmaanMultiDomainExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class KunstmaanMultiDomainExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions(): array
    {
        return [new KunstmaanMultiDomainExtension()];
    }

    public function testCorrectParametersHaveBeenSet()
    {
        $this->setParameter('kunstmaan_admin.admin_firewall_name', 'main');

        $this->load([
            'hosts' => [
                'host_one' => [
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

        $this->assertContainerBuilderHasAlias('kunstmaan_admin.domain_configuration', 'kunstmaan_multi_domain.domain_configuration');
    }
}
