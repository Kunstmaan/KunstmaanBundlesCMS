<?php

namespace Kunstmaan\FormBundle\Tests\DependencyInjection;

use Kunstmaan\FormBundle\DependencyInjection\KunstmaanFormExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Kunstmaan\AdminBundle\Tests\AbstractPrependableExtensionTestCase;

/**
 * Class KunstmaanFormExtensionTest
 */
class KunstmaanFormExtensionTest extends AbstractPrependableExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [new KunstmaanFormExtension()];
    }


    public function testCorrectParametersHaveBeenSet()
    {
        $this->container->setParameter('kernel.root_dir', '/somewhere/over/the/rainbow');
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_form.form_mailer.class');
        $this->assertContainerBuilderHasParameter('kunstmaan_form.form_handler.class');
    }
}
