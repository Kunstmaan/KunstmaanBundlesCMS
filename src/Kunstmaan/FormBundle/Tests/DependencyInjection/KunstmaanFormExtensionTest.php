<?php

namespace Kunstmaan\FormBundle\Tests\DependencyInjection;

use Kunstmaan\FormBundle\DependencyInjection\KunstmaanFormExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class KunstmaanFormExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions(): array
    {
        return [new KunstmaanFormExtension()];
    }

    public function testCorrectParametersHaveBeenSet()
    {
        $this->container->setParameter('kernel.project_dir', '/somewhere/over/the/rainbow');
        $this->load();

        $this->assertContainerBuilderHasParameter('form_submission_rootdir', '/somewhere/over/the/rainbow/public/uploads/formsubmissions');
    }
}
