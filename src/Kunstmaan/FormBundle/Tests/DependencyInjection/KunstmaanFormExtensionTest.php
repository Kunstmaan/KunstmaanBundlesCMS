<?php

namespace Kunstmaan\FormBundle\Tests\DependencyInjection;

use Kunstmaan\FormBundle\DependencyInjection\KunstmaanFormExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Kernel;

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

        $this->assertContainerBuilderHasParameter('kunstmaan_form.form_mailer.class');
        $this->assertContainerBuilderHasParameter('kunstmaan_form.form_handler.class');

        $expectedFormSubmissionPath = '/somewhere/over/the/rainbow/web/uploads/formsubmissions';
        if (Kernel::VERSION_ID >= 40000) {
            $expectedFormSubmissionPath = '/somewhere/over/the/rainbow/public/uploads/formsubmissions';
        }
        $this->assertContainerBuilderHasParameter('form_submission_rootdir', $expectedFormSubmissionPath);
    }
}
