<?php

namespace Kunstmaan\CookieBundle\Generator;

use Kunstmaan\GeneratorBundle\Generator\KunstmaanGenerator;
use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Class LegalGenerator
 */
class LegalGenerator extends KunstmaanGenerator
{
    /* @var BundleInterface */
    private $bundle;

    /* @var string */
    private $rootDir;

    /* @var string */
    private $prefix;

    /* @var bool */
    private $demosite;

    /* @var bool */
    private $overrideFiles;

    /**
     * @param string $prefix
     * @param string $rootDir
     * @param bool   $demosite
     */
    public function generate(BundleInterface $bundle, $prefix, $rootDir, $demosite, $overrideFiles)
    {
        $this->bundle = $bundle;
        $this->prefix = GeneratorUtils::cleanPrefix($prefix);
        $this->rootDir = $rootDir;
        $this->demosite = $demosite;
        $this->skeletonDir = __DIR__ . '/../Resources/skeleton/legal';
        $this->overrideFiles = $overrideFiles;

        $parameters = [
            'namespace' => $this->bundle->getNamespace(),
            'bundle' => $this->bundle,
            'bundle_name' => $this->bundle->getName(),
            'prefix' => $this->prefix,
            'demosite' => $this->demosite,
            'isV4' => $this->isSymfony4(),
        ];

        $this->generateAssets();
        $this->generateEntities($parameters);
        $this->generateFormTypes($parameters);
        $this->generatePagepartConfigs($parameters);
        $this->generatePagetemplateConfigs($parameters);
        $this->generateTemplates($parameters);

        if ($this->demosite) {
            $this->generateFixtures($parameters);
        }
    }

    /**
     * Generate the ui asset files.
     */
    private function generateAssets()
    {
        $sourceDir = $this->skeletonDir . '/Resources/ui/';

        if ($this->isSymfony4()) {
            $targetDir = $this->container->getParameter('kernel.project_dir') . '/assets/ui/';
        } else {
            $targetDir = $this->bundle->getPath() . '/Resources/ui/';
        }

        $this->copyFiles($sourceDir, $targetDir, true);
    }

    /**
     * Generate the entity classes.
     *
     * @param array $parameters The template parameters
     */
    public function generateEntities(array $parameters)
    {
        $relPath = '/Entity/Pages/';
        $sourceDir = $this->skeletonDir . $relPath;
        $targetDir = $this->bundle->getPath() . $relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'LegalFolderPage.php', $parameters, $this->overrideFiles);
        $this->renderSingleFile($sourceDir, $targetDir, 'LegalPage.php', $parameters, $this->overrideFiles);

        // Update homepage to add the Legal Folder Page as child.
        $homePage = $this->bundle->getPath() . '/Entity/Pages/HomePage.php';
        $phpCode = "            [\n";
        $phpCode .= "                'name' => 'Legal folder page',\n";
        $phpCode .= "                'class' => '" . $this->bundle->getNamespace() . "\\Entity\\Pages\\LegalFolderPage'\n";
        $phpCode .= '            ],' . "\n        ";

        if (file_exists($homePage)) {
            $data = file_get_contents($homePage);
            $data = preg_replace(
                '/(function\s*getPossibleChildTypes\s*\(\)\s*\{\s*)(return\s*\[|return\s*array\()/',
                "$1$2\n$phpCode",
                $data
            );
            file_put_contents($homePage, $data);
        }

        $this->assistant->writeLine('Generating pages : <info>OK</info>');

        $relPath = '/Entity/PageParts/';
        $sourceDir = $this->skeletonDir . $relPath;
        $targetDir = $this->bundle->getPath() . $relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'LegalCenteredIconPagePart.php', $parameters, $this->overrideFiles);
        $this->renderSingleFile($sourceDir, $targetDir, 'LegalTipPagePart.php', $parameters, $this->overrideFiles);
        $this->renderSingleFile($sourceDir, $targetDir, 'LegalIconTextPagePart.php', $parameters, $this->overrideFiles);
        $this->renderSingleFile($sourceDir, $targetDir, 'LegalCookiesPagePart.php', $parameters, $this->overrideFiles);
        $this->renderSingleFile($sourceDir, $targetDir, 'LegalOptInPagePart.php', $parameters, $this->overrideFiles);

        $this->assistant->writeLine('Generating pageparts : <info>OK</info>');
    }

    /**
     * Generate the form type classes.
     *
     * @param array $parameters The template parameters
     */
    public function generateFormTypes(array $parameters)
    {
        // Pages
        $relPath = '/Form/Pages/';
        $sourceDir = $this->skeletonDir . $relPath;
        $targetDir = $this->bundle->getPath() . $relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'LegalFolderPageAdminType.php', $parameters, $this->overrideFiles);
        $this->renderSingleFile($sourceDir, $targetDir, 'LegalPageAdminType.php', $parameters, $this->overrideFiles);

        $this->assistant->writeLine('Generating pages form types : <info>OK</info>');

        // PageParts
        $relPath = '/Form/PageParts/';
        $sourceDir = $this->skeletonDir . $relPath;
        $targetDir = $this->bundle->getPath() . $relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'LegalCenteredIconPagePartAdminType.php', $parameters, $this->overrideFiles);
        $this->renderSingleFile($sourceDir, $targetDir, 'LegalTipPagePartAdminType.php', $parameters, $this->overrideFiles);
        $this->renderSingleFile($sourceDir, $targetDir, 'LegalIconTextPagePartAdminType.php', $parameters, $this->overrideFiles);
        $this->renderSingleFile($sourceDir, $targetDir, 'LegalCookiesPagePartAdminType.php', $parameters, $this->overrideFiles);
        $this->renderSingleFile($sourceDir, $targetDir, 'LegalOptInPagePartAdminType.php', $parameters, $this->overrideFiles);

        $this->assistant->writeLine('Generating pageparts form types : <info>OK</info>');
    }

    /**
     * Generate the pagepart section configuration.
     *
     * @param array $parameters The template parameters
     */
    public function generatePagepartConfigs(array $parameters)
    {
        // Configuration pageparts
        if ($this->isSymfony4()) {
            $targetDir = $this->container->getParameter('kernel.project_dir') . '/config/kunstmaancms/pageparts/';
        } else {
            $targetDir = sprintf('%s/Resources/config/pageparts/', $this->bundle->getPath());
        }

        $sourceDir = sprintf('%s/Resources/config/pageparts/', $this->skeletonDir);

        $this->renderSingleFile($sourceDir, $targetDir, 'legal_header.yml', $parameters, $this->overrideFiles);
        $this->renderSingleFile($sourceDir, $targetDir, 'legal_main.yml', $parameters, $this->overrideFiles);

        $this->assistant->writeLine('Generating pagepart configuration : <info>OK</info>');
    }

    /**
     * Generate the page template configuration.
     *
     * @param array $parameters The template parameters
     */
    public function generatePagetemplateConfigs(array $parameters)
    {
        // Configuration templates
        if ($this->isSymfony4()) {
            $targetDir = $this->container->getParameter('kernel.project_dir') . '/config/kunstmaancms/pagetemplates/';
        } else {
            $targetDir = sprintf('%s/Resources/config/pagetemplates/', $this->bundle->getPath());
        }

        $sourceDir = sprintf('%s/Resources/config/pagetemplates/', $this->skeletonDir);

        $this->renderSingleFile($sourceDir, $targetDir, 'legalpage.yml', $parameters, $this->overrideFiles);

        $this->assistant->writeLine('Generating pagetemplate configuration : <info>OK</info>');
    }

    /**
     * Generate the twig templates.
     *
     * @param array $parameters The template parameters
     */
    public function generateTemplates(array $parameters)
    {
        // Pages
        $relPath = 'Pages/LegalPage';
        $targetDir = sprintf('%s/%s/', $this->getTemplateDir($this->bundle), $relPath);
        $sourceDir = sprintf('%s/Resources/views/%s/', $this->skeletonDir, $relPath);

        $this->renderSingleFile($sourceDir, $targetDir, '_content.html.twig', $parameters, $this->overrideFiles);
        $this->renderSingleFile($sourceDir, $targetDir, '_main.html.twig', $parameters, $this->overrideFiles);
        $this->renderSingleFile($sourceDir, $targetDir, 'pagetemplate.html.twig', $parameters, $this->overrideFiles);
        $this->renderSingleFile($sourceDir, $targetDir, 'view.html.twig', $parameters, $this->overrideFiles);

        $pageParts = ['LegalCenteredIconPagePart', 'LegalTipPagePart', 'LegalIconTextPagePart', 'LegalCookiesPagePart', 'LegalOptInPagePart'];
        foreach ($pageParts as $pagePart) {
            $relPath = 'PageParts';
            $targetDir = sprintf('%s/%s/%s/', $this->getTemplateDir($this->bundle), $relPath, $pagePart);
            $sourceDir = sprintf('%s/Resources/views/%s/%s/', $this->skeletonDir, $relPath, $pagePart);

            $this->renderFiles($sourceDir, $targetDir, $parameters, $this->overrideFiles);
        }

        $this->assistant->writeLine('Generating template files : <info>OK</info>');
    }

    /**
     * Generate the data fixtures classes.
     *
     * @param array $parameters The template parameters
     */
    public function generateFixtures(array $parameters)
    {
        $relPath = '/DataFixtures/ORM/LegalGenerator/';
        $sourceDir = $this->skeletonDir . $relPath;
        $targetDir = $this->bundle->getPath() . $relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'DefaultFixtures.php', $parameters, $this->overrideFiles);

        $this->assistant->writeLine('Generating fixtures : <info>OK</info>');
    }
}
