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
     * @var bool
     */
    private $demosite;

    /**
     * Generate the basic layout.
     *
     * @param BundleInterface $bundle         The bundle
     * @param string          $rootDir        The root directory of the application
     */
    public function generate(BundleInterface $bundle, $rootDir, $demosite)
    {
        $this->bundle = $bundle;
        $this->rootDir = $rootDir;
        $this->demosite = $demosite;

        $this->generateBowerFiles();
        $this->generateGulpFiles();
        $this->generateGemsFile();
        $this->generateAssets();
        $this->generateTemplate();
    }

    /**
     * Generate the bower configuration files.
     */
    private function generateBowerFiles()
    {
        $this->renderFiles($this->skeletonDir.'/bower/', $this->rootDir, array('bundle' => $this->bundle), true);
        $this->renderSingleFile($this->skeletonDir.'/bower/', $this->rootDir, '.bowerrc', array('bundle' => $this->bundle), true);
        $this->assistant->writeLine('Generating bower configuration : <info>OK</info>');
    }

    /**
     * Generate the gulp configuration files.
     */
    private function generateGulpFiles()
    {
        $this->renderFiles($this->skeletonDir.'/gulp/', $this->rootDir, array('bundle' => $this->bundle), true);
        $this->renderSingleFile($this->skeletonDir.'/gulp/', $this->rootDir, '.jshintrc', array('bundle' => $this->bundle), true);
        $this->renderSingleFile($this->skeletonDir.'/gulp/', $this->rootDir, '.groundcontrolrc', array('bundle' => $this->bundle), true);
        $this->assistant->writeLine('Generating gulp configuration : <info>OK</info>');
    }

    /**
     * Generate the gems configuration file.
     */
    private function generateGemsFile()
    {
        $this->renderFiles($this->skeletonDir.'/gems/', $this->rootDir, array('bundle' => $this->bundle), true);
        $this->assistant->writeLine('Generating gems configuration : <info>OK</info>');
    }

    /**
     * Generate the ui asset files.
     */
    private function generateAssets()
    {
        $sourceDir = $this->skeletonDir;
        $targetDir = $this->bundle->getPath();

        $relPath = '/Resources/ui/';
        $this->copyFiles($sourceDir.$relPath, $targetDir.$relPath, true);
        $this->renderFiles($sourceDir.$relPath.'/js/', $targetDir.$relPath.'/js/', array('bundle' => $this->bundle, 'demosite' => $this->demosite), true);
        $this->renderFiles($sourceDir.$relPath.'/scss/', $targetDir.$relPath.'/scss/', array('bundle' => $this->bundle, 'demosite' => $this->demosite), true);

        $this->assistant->writeLine('Generating ui assets : <info>OK</info>');
    }

    /**
     * Generate the twig template files.
     */
    private function generateTemplate()
    {
        $relPath = '/Resources/views/';
        $this->renderFiles($this->skeletonDir.$relPath, $this->bundle->getPath().$relPath, array('bundle' => $this->bundle, 'demosite' => $this->demosite), true);

        $this->assistant->writeLine('Generating template files : <info>OK</info>');
    }
}
