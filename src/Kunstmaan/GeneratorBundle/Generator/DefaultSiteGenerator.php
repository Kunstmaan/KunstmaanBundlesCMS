<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\Container;

use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Generates a default website using several Kunstmaan bundles using default templates and assets
 */
class DefaultSiteGenerator extends \Sensio\Bundle\GeneratorBundle\Generator\Generator
{

    private $filesystem;
    private $skeletonDir;

    public function __construct(Filesystem $filesystem, $skeletonDir)
    {
        $this->filesystem = $filesystem;
        $this->skeletonDir = $skeletonDir;
    }

    public function generate($bundle, OutputInterface $output, $rootDir)
    {

        $parameters = array(
            'namespace'         => $bundle->getNamespace(),
            'bundle'            => $bundle,
        );

        $this->generateEntities($bundle, $parameters, $output);
        $this->generateForm($bundle, $parameters, $output);
        $this->generatePagepartConfigs($bundle, $parameters, $output);
        $this->generateFixtures($bundle, $parameters, $output);
        $this->generateAssets($bundle, $parameters, $output);
        $this->generateTemplates($bundle, $parameters, $output, $rootDir);
    }

    public function generateTemplates($bundle, $parameters, $output, $rootDir)
    {
        $dirPath = $bundle->getPath();
        $fullSkeletonDir = $this->skeletonDir . '/resources/views';

        $this->filesystem->copy($fullSkeletonDir . '/Default/index.html.twig', $dirPath . '/Resources/views/Default/index.html.twig');
        GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Layout:layout.html.twig' %}\n", $dirPath . '/Resources/views/Default/index.html.twig');

        $this->renderFile($fullSkeletonDir, '/Page/layout.html.twig', $dirPath . '/Resources/views/Page/layout.html.twig', $parameters);

        $this->filesystem->copy($fullSkeletonDir . '/ContentPage/view.html.twig', $dirPath . '/Resources/views/ContentPage/view.html.twig');
        GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Resources/views/ContentPage/view.html.twig');

        $this->filesystem->copy($fullSkeletonDir . '/Form/fields.html.twig', $dirPath . '/Resources/views/Form/fields.html.twig');

        $this->filesystem->copy($fullSkeletonDir . '/FormPage/view.html.twig', $dirPath . '/Resources/views/FormPage/view.html.twig');
        GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Resources/views/FormPage/view.html.twig');
        GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/Resources/views/FormPage/view.html.twig');

        $this->filesystem->copy($fullSkeletonDir . '/HomePage/view.html.twig', $dirPath . '/Resources/views/HomePage/view.html.twig');
        GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Resources/views/HomePage/view.html.twig');

        $this->filesystem->copy($fullSkeletonDir . '/Layout/layout.html.twig', $dirPath . '/Resources/views/Layout/layout.html.twig');
        GeneratorUtils::replace("~~~CSS~~~", "{% include '" . $bundle->getName() .":Layout:_css.html.twig' %}\n", $dirPath . '/Resources/views/Layout/layout.html.twig');
        GeneratorUtils::replace("~~~TOP_JS~~~", "{% include '" . $bundle->getName() .":Layout:_js_header.html.twig' %}\n", $dirPath . '/Resources/views/Layout/layout.html.twig');
        GeneratorUtils::replace("~~~FOOTER_JS~~~", "{% include '" . $bundle->getName() .":Layout:_js_footer.html.twig' %}\n", $dirPath . '/Resources/views/Layout/layout.html.twig');

        $this->renderFile($fullSkeletonDir, '/Layout/_css.html.twig', $dirPath . '/Resources/views/Layout/_css.html.twig', $parameters);
        $this->renderFile($fullSkeletonDir, '/Layout/_js_footer.html.twig', $dirPath . '/Resources/views/Layout/_js_footer.html.twig', $parameters);
        $this->renderFile($fullSkeletonDir, '/Layout/_js_header.html.twig', $dirPath . '/Resources/views/Layout/_js_header.html.twig', $parameters);

        $this->filesystem->copy($fullSkeletonDir . '/Elastica/ContentPage.elastica.twig', $dirPath . '/Resources/views/Elastica/ContentPage.elastica.twig');
        $this->filesystem->copy($fullSkeletonDir . '/Elastica/FormPage.elastica.twig', $dirPath . '/Resources/views/Elastica/FormPage.elastica.twig');
        $this->filesystem->copy($fullSkeletonDir . '/Elastica/HomePage.elastica.twig', $dirPath . '/Resources/views/Elastica/HomePage.elastica.twig');

        $output->writeln('Generating Twig Templates : <info>OK</info>');

        GeneratorUtils::replace("[ \"KunstmaanAdminBundle\"", "[ \"KunstmaanAdminBundle\", \"". $bundle->getName()  ."\"", $rootDir . '/config/config.yml');

        $output->writeln('Configure assetic : <info>OK</info>');
    }

    public function generateAssets($bundle, $parameters, $output)
    {
        $dirPath = $bundle->getPath();
        $fullSkeletonDir = $this->skeletonDir . '/resources/public/css';

        $this->filesystem->copy($fullSkeletonDir . '/style.css', $dirPath . '/Resources/public/css/style.css', $parameters);

        $fullSkeletonDir = $this->skeletonDir . '/resources/public/js';

        $this->filesystem->copy($fullSkeletonDir . '/script.js', $dirPath . '/Resources/public/js/script.js');
        $this->filesystem->copy($fullSkeletonDir . '/libs/boxsizing.htc', $dirPath . '/Resources/public/js/libs/boxsizing.htc');
        $this->filesystem->copy($fullSkeletonDir . '/libs/jquery-1.8.1.min.js', $dirPath . '/Resources/public/js/libs/jquery-1.8.1.min.js');
        $this->filesystem->copy($fullSkeletonDir . '/libs/modernizr-2.6.2.min.js', $dirPath . '/Resources/public/js/libs/modernizr-2.6.2.min.js');
        $this->filesystem->copy($fullSkeletonDir . '/libs/respond.min.js', $dirPath . '/Resources/public/js/libs/respond.min.js');

        $fullSkeletonDir = $this->skeletonDir . '/resources/public/sass';

        $this->filesystem->copy($fullSkeletonDir . '/_480.scss', $dirPath . '/Resources/public/sass/_480.scss');
        $this->filesystem->copy($fullSkeletonDir . '/_768.scss', $dirPath . '/Resources/public/sass/_768.scss');
        $this->filesystem->copy($fullSkeletonDir . '/_1024.scss', $dirPath . '/Resources/public/sass/_1024.scss');
        $this->filesystem->copy($fullSkeletonDir . '/_debug.scss', $dirPath . '/Resources/public/sass/_debug.scss');
        $this->filesystem->copy($fullSkeletonDir . '/_fallbacks.scss', $dirPath . '/Resources/public/sass/_fallbacks.scss');
        $this->filesystem->copy($fullSkeletonDir . '/_main.scss', $dirPath . '/Resources/public/sass/_main.scss');
        $this->filesystem->copy($fullSkeletonDir . '/_mixins.scss', $dirPath . '/Resources/public/sass/_mixins.scss');
        $this->filesystem->copy($fullSkeletonDir . '/_normalizer.scss', $dirPath . '/Resources/public/sass/_normalizer.scss');
        $this->filesystem->copy($fullSkeletonDir . '/_print.scss', $dirPath . '/Resources/public/sass/_print.scss');
        $this->filesystem->copy($fullSkeletonDir . '/style.scss', $dirPath . '/Resources/public/sass/style.scss');

        $fullSkeletonDir = $this->skeletonDir . '/resources/public';

        $this->filesystem->copy($fullSkeletonDir . '/apple-touch-icon-57x57-precomposed.png', $dirPath . '/Resources/public/apple-touch-icon-57x57-precomposed.png');
        $this->filesystem->copy($fullSkeletonDir . '/apple-touch-icon-72x72-precomposed.png', $dirPath . '/Resources/public/apple-touch-icon-72x72-precomposed.png');
        $this->filesystem->copy($fullSkeletonDir . '/apple-touch-icon-114x114-precomposed.png', $dirPath . '/Resources/public/apple-touch-icon-114x114-precomposed.png');
        $this->filesystem->copy($fullSkeletonDir . '/apple-touch-icon-precomposed.png', $dirPath . '/Resources/public/apple-touch-icon-precomposed.png');
        $this->filesystem->copy($fullSkeletonDir . '/apple-touch-icon.png', $dirPath . '/Resources/public/apple-touch-icon.png');
        $this->filesystem->copy($fullSkeletonDir . '/favicon.ico', $dirPath . '/Resources/public/favicon.ico');

        $output->writeln('Generating Assets : <info>OK</info>');
    }

    public function generateFixtures($bundle, $parameters, $output)
    {
        $dirPath = $bundle->getPath() . '/DataFixtures/ORM';
        $fullSkeletonDir = $this->skeletonDir . '/datafixtures/orm';

        /* Default Site Fixtures */

        $classname = 'DefaultSiteFixtures';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        $output->writeln('Generating Fixtures : <info>OK</info>');
    }

    public function generatePagepartConfigs($bundle, $parameters, $output)
    {
        $dirPath = $bundle->getPath() . '/PagePartAdmin';
        $fullSkeletonDir = $this->skeletonDir . '/pagepartadmin';

        /* Banner */

        $classname = 'BannerPagePartAdminConfigurator';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        /* Content page */

        $classname = 'ContentPagePagePartAdminConfigurator';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        /* Form page */

        $classname = 'FormPagePagePartAdminConfigurator';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        $output->writeln('Generating forms : <info>OK</info>');

        /* Home page */

        $classname = 'HomePagePagePartAdminConfigurator';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        $output->writeln('Generating PagePart Configurators : <info>OK</info>');
    }

    public function generateForm($bundle, $parameters, $output)
    {
        $dirPath = $bundle->getPath() . '/Form';
        $fullSkeletonDir = $this->skeletonDir . '/form';

        /* Content page */

        $classname = 'ContentPageAdminType';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        /* Form page */

        $classname = 'FormPageAdminType';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        /* Home page */

        $classname = 'HomePageAdminType';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        $output->writeln('Generating forms : <info>OK</info>');
    }

    public function generateEntities($bundle, $parameters, $output)
    {
        $dirPath = $bundle->getPath() . '/Entity';
        $fullSkeletonDir = $this->skeletonDir . '/entity';

        /* Content page */

        $classname = 'ContentPage';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        /* Form page */

        $classname = 'FormPage';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        /* Home page */

        $classname = 'HomePage';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        $output->writeln('Generating entities : <info>OK</info>');
    }

}