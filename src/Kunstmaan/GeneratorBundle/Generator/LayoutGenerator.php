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
     * @var string
     */
    private $shortBundleName;

    /**
     * @var bool
     */
    private $demosite;

    /**
     * @var string
     */
    private $browserSyncUrl;

    /**
     * Generate the basic layout.
     *
     * @param BundleInterface $bundle  The bundle
     * @param string          $rootDir The root directory of the application
     */
    public function generate(BundleInterface $bundle, $rootDir, $demosite, $browserSyncUrl)
    {
        $this->bundle = $bundle;
        $this->rootDir = $rootDir;
        $this->demosite = $demosite;
        $this->browserSyncUrl = $browserSyncUrl;

        $this->shortBundleName = '@' . str_replace('Bundle', '', $bundle->getName());

        $this->generateGroundcontrolFiles();
        $this->generateAssets();
        $this->generateTemplate();
    }

    /**
     * Generate the groundcontrol(gulp) configuration files.
     */
    private function generateGroundcontrolFiles()
    {
        $this->renderFiles(
            $this->skeletonDir . '/groundcontrol/bin/',
            $this->rootDir . '/groundcontrol/',
            ['bundle' => $this->bundle, 'demosite' => $this->demosite, 'browserSyncUrl' => $this->browserSyncUrl, 'isV4' => $this->isSymfony4()],
            true
        );
        $this->renderSingleFile(
            $this->skeletonDir . '/groundcontrol/',
            $this->rootDir,
            '.babelrc',
            ['bundle' => $this->bundle],
            true
        );
        $this->renderSingleFile(
            $this->skeletonDir . '/groundcontrol/',
            $this->rootDir,
            '.eslintrc',
            ['bundle' => $this->bundle, 'demosite' => $this->demosite],
            true
        );
        $this->renderSingleFile(
            $this->skeletonDir . '/groundcontrol/',
            $this->rootDir,
            '.nvmrc',
            ['bundle' => $this->bundle],
            true
        );
        $this->renderSingleFile(
            $this->skeletonDir . '/groundcontrol/',
            $this->rootDir,
            '.stylelintrc',
            ['bundle' => $this->bundle],
            true
        );
        $this->renderExecutableFile(
            $this->skeletonDir . '/groundcontrol/',
            $this->rootDir,
            'buildUI.sh',
            ['bundle' => $this->bundle],
            true
        );
        $this->renderSingleFile(
            $this->skeletonDir . '/groundcontrol/',
            $this->rootDir,
            'gulpfile.babel.js',
            ['bundle' => $this->bundle, 'demosite' => $this->demosite, 'isV4' => $this->isSymfony4()],
            true
        );
        $this->renderSingleFile(
            $this->skeletonDir . '/groundcontrol/',
            $this->rootDir,
            'package.json',
            ['bundle' => $this->bundle, 'demosite' => $this->demosite],
            true
        );
        $this->assistant->writeLine('Generating groundcontrol configuration : <info>OK</info>');
    }

    /**
     * Generate the ui asset files.
     */
    private function generateAssets()
    {
        $sourceDir = $this->skeletonDir;

        $relPath = '/Resources/ui/';
        $this->copyFiles($sourceDir . $relPath, $this->getAssetsDir($this->bundle) . '/ui', true);
        $this->renderFiles(
            $sourceDir . $relPath . '/js/',
            $this->getAssetsDir($this->bundle) . '/ui/js/',
            ['bundle' => $this->bundle, 'demosite' => $this->demosite],
            true
        );
        $this->renderFiles(
            $sourceDir . $relPath . '/scss/',
            $this->getAssetsDir($this->bundle) . '/ui/scss/',
            ['bundle' => $this->bundle, 'demosite' => $this->demosite],
            true
        );

        if (!$this->demosite) {
            // Files
            $this->removeDirectory($this->getAssetsDir($this->bundle) . '/ui/files/');

            // Images
            $this->removeDirectory($this->getAssetsDir($this->bundle) . '/ui/fonts/');

            // JS
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/js/search.js');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/js/demoMsg.js');

            // Images
            $this->removeDirectory($this->getAssetsDir($this->bundle) . '/ui/img/demosite/');
            $this->removeDirectory($this->getAssetsDir($this->bundle) . '/ui/img/buttons/');
            $this->removeDirectory($this->getAssetsDir($this->bundle) . '/ui/img/dummy/');
            $this->removeDirectory($this->getAssetsDir($this->bundle) . '/ui/img/backgrounds/');

            // SCSS
            // SCSS - Blocks
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/blocks/_img-icon.scss');

            // SCSS - Forms
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/forms/_form-widget.scss');

            // SCSS - Structures
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/structures/_splash.scss');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/structures/docs/splash.md');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/structures/_submenu.scss');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/structures/docs/submenu.md');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/structures/_blog-item.scss');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/structures/docs/blog-item.md');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/structures/docs/blog-item-summary.md');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/structures/_search-results.scss');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/structures/docs/search-results.md');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/structures/_breadcrumb-nav.scss');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/structures/docs/breadcrumb.md');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/structures/_header-visual.scss');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/structures/docs/header-visual.md');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/structures/_newsletter.scss');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/structures/docs/newsletter.md');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/structures/_pagination.scss');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/structures/docs/pagination.md');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/structures/_demosite-msg.scss');

            // SCSS - Header
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/header/_main-nav.scss');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/header/_site-nav.scss');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/header/_language-nav.scss');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/header/_contact-nav.scss');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/header/_search-form.scss');

            // SCSS - Footer
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/footer/_social-footer.scss');

            // SCSS - Pageparts
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/components/pageparts/_service-pp.scss');

            // SCSS - Config
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/config/vendors/_cargobay-imports.scss');
            $this->removeFile($this->getAssetsDir($this->bundle) . '/ui/scss/config/vendors/_cargobay-vars.scss');

            // SCSS - Mixins
            $this->removeDirectory($this->getAssetsDir($this->bundle) . '/ui/scss/helpers/mixins/');
        }

        $relPath = '/Resources/admin/';
        $this->copyFiles($sourceDir . $relPath, $this->getAssetsDir($this->bundle) . '/admin', true);

        $this->assistant->writeLine('Generating ui assets : <info>OK</info>');
    }

    /**
     * Generate the twig template files.
     */
    private function generateTemplate()
    {
        $relPath = '/Resources/views/';
        $this->renderFiles(
            $this->skeletonDir . $relPath,
            $this->getTemplateDir($this->bundle),
            ['bundle' => $this->bundle, 'demosite' => $this->demosite, 'shortBundleName' => $this->shortBundleName, 'isV4' => $this->isSymfony4()],
            true
        );

        if (!$this->demosite) {
            // Layout
            $this->removeFile($this->getTemplateDir($this->bundle) . '/Layout/_mobile-nav.html.twig');
            $this->removeFile($this->getTemplateDir($this->bundle) . '/Layout/_demositemessage.html.twig');
        }

        $this->assistant->writeLine('Generating template files : <info>OK</info>');
    }
}
