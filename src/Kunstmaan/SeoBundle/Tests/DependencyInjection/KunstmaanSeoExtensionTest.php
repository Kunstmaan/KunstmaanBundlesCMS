<?php

namespace Kunstmaan\SeoBundle\Tests\DependencyInjection;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\CloneHelper;
use Kunstmaan\SeoBundle\DependencyInjection\KunstmaanSeoExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Contracts\Translation\TranslatorInterface;

class KunstmaanSeoExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->registerService('doctrine.orm.entity_manager', EntityManagerInterface::class);
        $this->registerService('kunstmaan_admin.clone.helper', CloneHelper::class);
        $this->registerService('security.authorization_checker', AuthorizationChecker::class);
        $this->registerService('translator', TranslatorInterface::class);
    }

    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions(): array
    {
        return [new KunstmaanSeoExtension()];
    }

    public function testSeoRequestCacheMethodCall()
    {
        $this->setDefinition('kunstmaan_seo.twig.extension', new Definition());
        $this->setDefinition('cache.app', new Definition());

        $this->load(['request_cache' => 'cache.app']);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'kunstmaan_seo.twig.extension',
            'setRequestCache',
            [
                new Reference('cache.app'),
            ]
        );
    }

    public function testSeoRequestCacheMethodCallWithNullValue()
    {
        $this->setDefinition('kunstmaan_seo.twig.extension', new Definition());
        $this->setDefinition('cache.app', new Definition());

        $this->load(['request_cache' => null]);

        $this->compile();

        $this->assertFalse($this->container->getDefinition('kunstmaan_seo.twig.extension')->hasMethodCall('setRequestCache'));
    }
}
