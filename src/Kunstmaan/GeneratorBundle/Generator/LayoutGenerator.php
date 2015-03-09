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

        $this->generateBowerFiles();
        $this->generateGulpFiles();
        $this->generateJshintrcFile();
        $this->generateGroundcontrolrcFile();
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
        $this->assistant->writeLine('Generating gulp configuration : <info>OK</info>');
    }

    /**
     * Generate the jshint configuration file.
     */
    private function generateJshintrcFile()
    {
        $this->renderSingleFile($this->skeletonDir.'/gulp/', $this->rootDir, '.jshintrc', array('bundle' => $this->bundle), true);
        $this->assistant->writeLine('Generating jshint configuration : <info>OK</info>');
    }

    /**
     * Generate the groundcontrol configuration file.
     */
    private function generateGroundcontrolrcFile()
    {
        $this->renderSingleFile($this->skeletonDir.'/gulp/', $this->rootDir, '.groundcontrolrc', array('bundle' => $this->bundle), true);
        $this->assistant->writeLine('Generating groundcontrol configuration : <info>OK</info>');
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
        $this->renderFiles($sourceDir.$relPath, $targetDir.$relPath, array('bundle' => $this->bundle), true);

        $this->assistant->writeLine('Generating ui assets : <info>OK</info>');
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
