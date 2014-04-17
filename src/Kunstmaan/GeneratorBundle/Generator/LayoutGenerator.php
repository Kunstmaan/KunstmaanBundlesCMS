<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Generates all layout files
 */
class LayoutGenerator extends KunstmaanGenerator
{
    /**
     * @var BundleInterface
     */
    private $bundle;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * Generate the basic layout.
     *
     * @param BundleInterface $bundle         The bundle
     * @param string          $rootDir        The root directory of the application
     */
    public function generate(BundleInterface $bundle, $rootDir)
    {
        $this->bundle = $bundle;
        $this->rootDir = $rootDir;

        $this->generateGruntFiles();
        $this->generateBowerFiles();
        $this->generateAssets();
        $this->generateTemplate();
    }

    /**
     * Generate the grunt configuration files.
     */
    private function generateGruntFiles()
    {
        $this->renderFiles($this->skeletonDir.'/grunt/', $this->rootDir, array('bundle' => $this->bundle), true);

        $this->assistant->writeLine('Generating grunt configuration : <info>OK</info>');
    }

    /**
     * Generate the bower configuration files.
     */
    private function generateBowerFiles()
    {
        $this->renderFiles($this->skeletonDir.'/bower/', $this->rootDir, array('bundle' => $this->bundle), true);
        $this->assistant->writeLine('Generating bower configuration : <info>OK</info>');
    }

    /**
     * Generate the public asset files.
     */
    private function generateAssets()
    {
        $sourceDir = $this->skeletonDir;
        $targetDir = $this->bundle->getPath();

        $relPath = '/Resources/public/';
        $this->copyFiles($sourceDir.$relPath, $targetDir.$relPath, true);

        $relPath = '/Resources/public/scss/config/';
        $this->renderSingleFile($sourceDir.$relPath, $targetDir.$relPath, '_paths.scss', array('bundle' => $this->bundle), true);

        $this->assistant->writeLine('Generating public assets : <info>OK</info>');
    }

    /**
     * Generate the twig template files.
     */
    private function generateTemplate()
    {
        $relPath = '/Resources/views/';
        $this->renderFiles($this->skeletonDir.$relPath, $this->bundle->getPath().$relPath, array('bundle' => $this->bundle), true);

        $this->assistant->writeLine('Generating template files : <info>OK</info>');
    }
}
