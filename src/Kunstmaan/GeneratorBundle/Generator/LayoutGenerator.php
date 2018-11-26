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

        $this->shortBundleName = '@'.str_replace('Bundle', '', $bundle->getName());

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
            array('bundle' => $this->bundle, 'demosite' => $this->demosite, 'browserSyncUrl' => $this->browserSyncUrl),
            true
        );
        $this->renderSingleFile(
            $this->skeletonDir . '/groundcontrol/',
            $this->rootDir,
            '.babelrc',
            array('bundle' => $this->bundle),
            true
        );
        $this->renderSingleFile(
            $this->skeletonDir . '/groundcontrol/',
            $this->rootDir,
            '.eslintrc',
            array('bundle' => $this->bundle, 'demosite' => $this->demosite),
            true
        );
        $this->renderSingleFile(
            $this->skeletonDir . '/groundcontrol/',
            $this->rootDir,
            '.nvmrc',
            array('bundle' => $this->bundle),
            true
        );
        $this->renderSingleFile(
            $this->skeletonDir . '/groundcontrol/',
            $this->rootDir,
            '.stylelintrc',
            array('bundle' => $this->bundle),
            true
        );
        $this->renderExecutableFile(
            $this->skeletonDir . '/groundcontrol/',
            $this->rootDir,
            'buildUI.sh',
            array('bundle' => $this->bundle),
            true
        );
        $this->renderSingleFile(
            $this->skeletonDir . '/groundcontrol/',
            $this->rootDir,
            'gulpfile.babel.js',
            array('bundle' => $this->bundle, 'demosite' => $this->demosite),
            true
        );
        $this->renderSingleFile(
            $this->skeletonDir . '/groundcontrol/',
            $this->rootDir,
            'package.json',
            array('bundle' => $this->bundle, 'demosite' => $this->demosite),
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
        $targetDir = $this->bundle->getPath();

        $relPath = '/Resources/ui/';
        $this->copyFiles($sourceDir . $relPath, $targetDir . $relPath, true);
        $this->renderFiles(
            $sourceDir . $relPath . '/js/',
            $targetDir . $relPath . '/js/',
            array('bundle' => $this->bundle, 'demosite' => $this->demosite),
            true
        );
        $this->renderFiles(
            $sourceDir . $relPath . '/scss/',
            $targetDir . $relPath . '/scss/',
            array('bundle' => $this->bundle, 'demosite' => $this->demosite),
            true
        );

        if (!$this->demosite) {
            // Files
            $this->removeDirectory($targetDir . $relPath . '/files/');

            // Images
            $this->removeDirectory($targetDir . $relPath . '/fonts/');

            // JS
            $this->removeFile($targetDir . $relPath . '/js/search.js');
            $this->removeFile($targetDir . $relPath . '/js/demoMsg.js');

            // Images
            $this->removeDirectory($targetDir . $relPath . '/img/demosite/');
            $this->removeDirectory($targetDir . $relPath . '/img/buttons/');
            $this->removeDirectory($targetDir . $relPath . '/img/dummy/');
            $this->removeDirectory($targetDir . $relPath . '/img/backgrounds/');

            // SCSS
            // SCSS - Blocks
            $this->removeFile($targetDir . $relPath . '/scss/components/blocks/_img-icon.scss');

            // SCSS - Forms
            $this->removeFile($targetDir . $relPath . '/scss/components/forms/_form-widget.scss');

            // SCSS - Structures
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/_splash.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/docs/splash.md');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/_submenu.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/docs/submenu.md');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/_blog-item.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/docs/blog-item.md');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/docs/blog-item-summary.md');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/_search-results.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/docs/search-results.md');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/_breadcrumb-nav.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/docs/breadcrumb.md');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/_header-visual.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/docs/header-visual.md');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/_newsletter.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/docs/newsletter.md');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/_pagination.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/docs/pagination.md');
            $this->removeFile($targetDir . $relPath . '/scss/components/structures/_demosite-msg.scss');

            // SCSS - Header
            $this->removeFile($targetDir . $relPath . '/scss/components/header/_main-nav.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/header/_site-nav.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/header/_language-nav.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/header/_contact-nav.scss');
            $this->removeFile($targetDir . $relPath . '/scss/components/header/_search-form.scss');

            // SCSS - Footer
            $this->removeFile($targetDir . $relPath . '/scss/components/footer/_social-footer.scss');

            // SCSS - Pageparts
            $this->removeFile($targetDir . $relPath . '/scss/components/pageparts/_service-pp.scss');

            // SCSS - Config
            $this->removeFile($targetDir . $relPath . '/scss/config/vendors/_cargobay-imports.scss');
            $this->removeFile($targetDir . $relPath . '/scss/config/vendors/_cargobay-vars.scss');

            // SCSS - Mixins
            $this->removeDirectory($targetDir . $relPath . '/scss/helpers/mixins/');
        }

        $relPath = '/Resources/admin/';
        $this->copyFiles($sourceDir . $relPath, $targetDir . $relPath, true);

        $this->assistant->writeLine('Generating ui assets : <info>OK</info>');
    }

    /**
     * Generate the twig template files.
     */
    private function generateTemplate()
    {
        $targetDir = $this->bundle->getPath();

        $relPath = '/Resources/views/';
        $this->renderFiles(
            $this->skeletonDir . $relPath,
            $this->bundle->getPath() . $relPath,
            array('bundle' => $this->bundle, 'demosite' => $this->demosite, 'shortBundleName' => $this->shortBundleName),
            true
        );

        if (!$this->demosite) {
            // Layout
            $this->removeFile($targetDir . $relPath . '/Layout/_mobile-nav.html.twig');
            $this->removeFile($targetDir . $relPath . '/Layout/_demositemessage.html.twig');
        }

        $this->assistant->writeLine('Generating template files : <info>OK</info>');
    }
}
