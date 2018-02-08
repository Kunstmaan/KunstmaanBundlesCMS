<?php

namespace Tests\Kunstmaan\FormBundle\DependencyInjection;

use Kunstmaan\FormBundle\DependencyInjection\KunstmaanFormExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * Class KunstmaanFormExtensionTest
 */
class KunstmaanFormExtensionTest extends AbstractExtensionTestCase
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
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_form.form_mailer.class');
        $this->assertContainerBuilderHasParameter('kunstmaan_form.form_handler.class');
    }
}
