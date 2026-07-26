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

    public function testFileUploadAllowedExtensionsDefault()
    {
        $this->container->setParameter('kernel.project_dir', '/somewhere/over/the/rainbow');
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_form.file_upload.allowed_extensions');
        $allowed = $this->container->getParameter('kunstmaan_form.file_upload.allowed_extensions');
        $this->assertContains('pdf', $allowed);
        $this->assertContains('jpg', $allowed);
        $this->assertNotContains('php', $allowed);
        $this->assertNotContains('html', $allowed);
        $this->assertNotContains('svg', $allowed);
    }

    public function testFileUploadAllowedExtensionsCanBeOverridden()
    {
        $this->container->setParameter('kernel.project_dir', '/somewhere/over/the/rainbow');
        $this->load(['file_upload' => ['allowed_extensions' => ['pdf']]]);

        $this->assertContainerBuilderHasParameter('kunstmaan_form.file_upload.allowed_extensions', ['pdf']);
    }
}
