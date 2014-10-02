<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Generates a default website using several Kunstmaan bundles using default templates and assets
 */
class DefaultSiteGenerator extends KunstmaanGenerator
{
    /**
     * @var BundleInterface
     */
    private $bundle;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var bool
     */
    private $demosite;

    /**
     * Generate the website.
     *
     * @param BundleInterface $bundle
     * @param string $prefix
     * @param string $rootDir
     * @param bool $demosite
     */
    public function generate(BundleInterface $bundle, $prefix, $rootDir, $demosite = false)
    {
        $this->bundle = $bundle;
        $this->prefix = GeneratorUtils::cleanPrefix($prefix);
        $this->rootDir = $rootDir;
        $this->demosite = $demosite;

        $parameters = array(
            'namespace'   => $this->bundle->getNamespace(),
            'bundle'      => $this->bundle,
            'bundle_name' => $this->bundle->getName(),
            'prefix'      => $this->prefix,
            'demosite'    => $this->demosite
        );

        $this->generateControllers($parameters);
        $this->generateAdminLists($parameters);
        if ($this->isMultiLangEnvironment()) {
            $this->generateDefaultLocaleFallbackCode($parameters);
        }
        $this->generateEntities($parameters);
        $this->generateFormTypes($parameters);
        $this->generateMenuAdaptors($parameters);
        $this->generateFixtures($parameters);
        $this->generatePagepartConfigs($parameters);
        $this->generatePagetemplateConfigs($parameters);
        $this->generateConfig();
        $this->generateRouting($parameters);
        $this->generateTemplates($parameters);
    }

    /**
     * Generate controller classes.
     *
     * @param array $parameters
     */
    private function generateControllers(array $parameters)
    {
        $relPath = '/Controller/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'DefaultController.php', $parameters, true);

        if ($this->demosite) {
            $this->renderSingleFile($sourceDir, $targetDir, 'SatelliteAdminListController.php', $parameters, true);
        }

        $this->assistant->writeLine('Generating controllers : <info>OK</info>');
    }

    /**
     * Generate admin list classes.
     *
     * @param array $parameters
     */
    private function generateAdminLists(array $parameters)
    {
        if ($this->demosite) {
            $relPath = '/AdminList/';
            $this->renderFiles($this->skeletonDir.$relPath, $this->bundle->getPath().$relPath, $parameters, true);

            $this->assistant->writeLine('Generating admin lists : <info>OK</info>');
        }
    }

    /**
     * Generate event listener class.
     *
     * @param array $parameters
     */
    public function generateDefaultLocaleFallbackCode(array $parameters)
    {
        $relPath = '/EventListener/';
        $this->renderFiles($this->skeletonDir.$relPath, $this->bundle->getPath().$relPath, $parameters, true);

        $relPath = '/Resources/config/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;
        $this->renderSingleFile($sourceDir, $targetDir, 'services.yml', $parameters, true);

        $this->assistant->writeLine('Generating code for defaultlocale fallback : <info>OK</info>');
    }

    /**
     * Generate the entity classes.
     *
     * @param array $parameters The template parameters
     */
    public function generateEntities(array $parameters)
    {
        $relPath = '/Entity/Pages/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'HomePage.php', $parameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'ContentPage.php', $parameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'BehatTestPage.php', $parameters);

        if ($this->demosite) {
            $this->renderSingleFile($sourceDir, $targetDir, 'FormPage.php', $parameters);
            $this->renderSingleFile($sourceDir, $targetDir, 'SatelliteOverviewPage.php', $parameters);
        }

        if ($this->demosite) {
            $relPath = '/Entity/PageParts/';
            $sourceDir = $this->skeletonDir.$relPath;
            $targetDir = $this->bundle->getPath().$relPath;

            $this->renderSingleFile($sourceDir, $targetDir, 'SlidePagePart.php', $parameters);
        }

        if ($this->demosite) {
            $relPath = '/Entity/';
            $sourceDir = $this->skeletonDir.$relPath;
            $targetDir = $this->bundle->getPath().$relPath;

            $this->renderSingleFile($sourceDir, $targetDir, 'Satellite.php', $parameters);
        }

        $this->assistant->writeLine('Generating entities : <info>OK</info>');
    }

    /**
     * Generate the form type classes.
     *
     * @param array $parameters The template parameters
     */
    public function generateFormTypes(array $parameters)
    {
        $relPath = '/Form/Pages/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'HomePageAdminType.php', $parameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'ContentPageAdminType.php', $parameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'BehatTestPageAdminType.php', $parameters);

        if ($this->demosite) {
            $this->renderSingleFile($sourceDir, $targetDir, 'FormPageAdminType.php', $parameters);
            $this->renderSingleFile($sourceDir, $targetDir, 'SatelliteOverviewPageAdminType.php', $parameters);
        }

        $relPath = '/Form/PageParts/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        if ($this->demosite) {
            $this->renderSingleFile($sourceDir, $targetDir, 'SlidePagePartAdminType.php', $parameters);
        }

        $relPath = '/Form/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        if ($this->demosite) {
            $this->renderSingleFile($sourceDir, $targetDir, 'SatelliteAdminType.php', $parameters);
        }

        $this->assistant->writeLine('Generating form types : <info>OK</info>');
    }

    /**
     * Generate the menu adaptors classes.
     *
     * @param array $parameters The template parameters
     */
    public function generateMenuAdaptors(array $parameters)
    {
        if ($this->demosite) {
            $relPath = '/Helper/Menu/';
            $sourceDir = $this->skeletonDir.$relPath;
            $targetDir = $this->bundle->getPath().$relPath;

            $this->renderSingleFile($sourceDir, $targetDir, 'AdminMenuAdaptor.php', $parameters);

            $file = $this->bundle->getPath().'/Resources/config/services.yml';
            if (!is_file($file)) {
                $ymlData = "services:";
            } else {
                $ymlData = "";
            }
            $ymlData .= "\n\n    ".strtolower($this->bundle->getName()).".admin_menu_adaptor:";
            $ymlData .= "\n        class: ".$this->bundle->getNamespace()."\Helper\Menu\AdminMenuAdaptor";
            $ymlData .= "\n        arguments: [\"@security.context\"]";
            $ymlData .= "\n        tags:";
            $ymlData .= "\n            -  { name: 'kunstmaan_admin.menu.adaptor' }\n";
            file_put_contents($file, $ymlData, FILE_APPEND);

            $this->assistant->writeLine('Generating menu adaptors : <info>OK</info>');
        }
    }

    /**
     * Generate the data fixtures classes.
     *
     * @param array $parameters The template parameters
     */
    public function generateFixtures(array $parameters)
    {
        $relPath = '/DataFixtures/ORM/DefaultSiteGenerator/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'DefaultSiteFixtures.php', $parameters);

        if ($this->demosite) {
            $this->renderSingleFile($sourceDir, $targetDir, 'SliderFixtures.php', $parameters);
            $this->renderSingleFile($sourceDir, $targetDir, 'SitemapFixtures.php', $parameters);
        }

        $this->assistant->writeLine('Generating fixtures : <info>OK</info>');
    }

    /**
     * Generate the pagepart section configuration.
     *
     * @param array $parameters The template parameters
     */
    public function generatePagepartConfigs(array $parameters)
    {
        $relPath = '/Resources/config/pageparts/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'footer.yml', $parameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'home.yml', $parameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'left-column.yml', $parameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'main.yml', $parameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'middle-column.yml', $parameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'right-column.yml', $parameters);

        if ($this->demosite) {
            $this->renderSingleFile($sourceDir, $targetDir, 'form.yml', $parameters);
            $this->renderSingleFile($sourceDir, $targetDir, 'slider.yml', $parameters);
        }

        $this->assistant->writeLine('Generating pagepart configuration : <info>OK</info>');
    }

    /**
     * Generate the page template configuration.
     *
     * @param array $parameters The template parameters
     */
    public function generatePagetemplateConfigs(array $parameters)
    {
        $relPath = '/Resources/config/pagetemplates/';
        $sourceDir = $this->skeletonDir . $relPath;
        $targetDir = $this->bundle->getPath() . $relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'homepage.yml', $parameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'contentpage.yml', $parameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'behat-test-page.yml', $parameters);

        if ($this->demosite) {
            $this->renderSingleFile($sourceDir, $targetDir, 'homepage-no-slider.yml', $parameters);
            $this->renderSingleFile($sourceDir, $targetDir, 'formpage.yml', $parameters);
            $this->renderSingleFile($sourceDir, $targetDir, 'satelliteoverviewpage.yml', $parameters);
        }

        $this->assistant->writeLine('Generating pagetemplate configuration : <info>OK</info>');
    }

    /**
     * Append to the application config file.
     */
    public function generateConfig()
    {
        $configFile = $this->rootDir.'/app/config/config.yml';

        $data = Yaml::parse($configFile);
        if (!array_key_exists('white_october_pagerfanta', $data)) {
            $ymlData = "\n\nwhite_october_pagerfanta:";
            $ymlData .= "\n    default_view: twitter_bootstrap\n";
            file_put_contents($configFile, $ymlData, FILE_APPEND);
        }
    }

    /**
     * Generate bundle routing configuration.
     * @param array $parameters The template parameters
     */
    public function generateRouting(array $parameters)
    {
        $relPath = '/Resources/config/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'routing.yml', $parameters, true);

        $this->assistant->writeLine('Generating routing : <info>OK</info>');
    }

    /**
     * Generate the twig templates.
     *
     * @param array $parameters The template parameters
     */
    public function generateTemplates(array $parameters)
    {
        if ($this->isMultiLangEnvironment()) {
            $relPath = '/Resources/views/Default/';
            $sourceDir = $this->skeletonDir.$relPath;
            $targetDir = $this->bundle->getPath().$relPath;

            $this->renderSingleFile($sourceDir, $targetDir, 'language-chooser.html.twig', $parameters);
        }

        $relPath = '/Resources/views/Layout/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'submenu.html.twig', $parameters);

        // Pages

        $relPath = '/Resources/views/Pages/HomePage/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'pagetemplate.html.twig', $parameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'view.html.twig', $parameters);
        if ($this->demosite) {
            $this->renderSingleFile($sourceDir, $targetDir, 'pagetemplate-no-slider.html.twig', $parameters);
            $this->renderSingleFile($sourceDir, $targetDir, 'slider.html.twig', $parameters);
        }

        $relPath = '/Resources/views/Pages/ContentPage/';
        $this->renderFiles($this->skeletonDir.$relPath, $this->bundle->getPath().$relPath, $parameters, true);

        if ($this->demosite) {
            $relPath = '/Resources/views/Pages/FormPage/';
            $this->renderFiles($this->skeletonDir.$relPath, $this->bundle->getPath().$relPath, $parameters, true);

            $relPath = '/Resources/views/Pages/SatelliteOverviewPage/';
            $this->renderFiles($this->skeletonDir.$relPath, $this->bundle->getPath().$relPath, $parameters, true);
        }

        // Pageparts

        if ($this->demosite) {
            $relPath = '/Resources/views/PageParts/SlidePagePart/';
            $this->renderFiles($this->skeletonDir.$relPath, $this->bundle->getPath().$relPath, $parameters, true);
        }

        // Error templates

        $relPath = '/Resources/views/Error/';
        $this->renderFiles($this->skeletonDir.$relPath, $this->bundle->getPath().$relPath, $parameters, true);

        $sourcePath = '/app/TwigBundle/';
        $targetPath = $this->rootDir.'/app/Resources/TwigBundle/';
        $this->renderFiles($this->skeletonDir.$sourcePath, $targetPath, $parameters, true);

        // Bundle overwrites

        if ($this->demosite) {
            $sourcePath = '/app/KunstmaanSitemapBundle/';
            $targetPath = $this->rootDir.'/app/Resources/KunstmaanSitemapBundle/';

            $this->renderFiles($this->skeletonDir.$sourcePath, $targetPath, $parameters, true);

            $sourcePath = '/app/KunstmaanArticleBundle/';
            $targetPath = $this->rootDir.'/app/Resources/KunstmaanArticleBundle/';
            $this->renderFiles($this->skeletonDir.$sourcePath, $targetPath, $parameters, true);

            $sourcePath = '/app/KunstmaanFormBundle/';
            $targetPath = $this->rootDir.'/app/Resources/KunstmaanFormBundle/';
            $this->renderFiles($this->skeletonDir.$sourcePath, $targetPath, $parameters, true);

        }

        $this->assistant->writeLine('Generating template files : <info>OK</info>');
    }

    /**
     * Returns true if we detect the site uses the locale.
     *
     * @return bool
     */
    private function isMultiLangEnvironment() {
        // This is a pretty silly implementation.
        // It just checks if it can find _locale in the routing.yml
        $routingFile = file_get_contents($this->rootDir.'/app/config/routing.yml');
        return preg_match('/_locale:/i', $routingFile);
    }
}
