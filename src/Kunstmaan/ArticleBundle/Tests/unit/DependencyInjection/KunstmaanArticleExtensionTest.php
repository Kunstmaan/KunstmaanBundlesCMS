<?php

namespace Kunstmaan\ArticleBundle\Tests\DependencyInjection;

use Kunstmaan\AdminBundle\Tests\unit\AbstractPrependableExtensionTestCase;
use Kunstmaan\ArticleBundle\DependencyInjection\KunstmaanArticleExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * Class KunstmaanArticleExtensionTest
 */
class KunstmaanArticleExtensionTest extends AbstractPrependableExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [new KunstmaanArticleExtension()];
    }

    public function testCorrectParametersHaveBeenSet()
    {
        $this->container->setParameter('empty_extension', true);
        $this->load();

        $this->assertContainerBuilderHasParameter('empty_extension', true);
    }
}
