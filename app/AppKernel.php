<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new FOS\HttpCacheBundle\FOSHttpCacheBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Kunstmaan\UtilitiesBundle\KunstmaanUtilitiesBundle(),
            new Kunstmaan\NodeBundle\KunstmaanNodeBundle(),
            new Kunstmaan\SeoBundle\KunstmaanSeoBundle(),
            new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new Kunstmaan\MediaBundle\KunstmaanMediaBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Kunstmaan\AdminBundle\KunstmaanAdminBundle(),
            new Kunstmaan\PagePartBundle\KunstmaanPagePartBundle(),
            new Kunstmaan\MediaPagePartBundle\KunstmaanMediaPagePartBundle(),
            new Kunstmaan\FormBundle\KunstmaanFormBundle(),
            new Kunstmaan\AdminListBundle\KunstmaanAdminListBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new Kunstmaan\SearchBundle\KunstmaanSearchBundle(),
            new Kunstmaan\NodeSearchBundle\KunstmaanNodeSearchBundle(),
            new Kunstmaan\SitemapBundle\KunstmaanSitemapBundle(),
            new Kunstmaan\ArticleBundle\KunstmaanArticleBundle(),
            new Kunstmaan\TranslatorBundle\KunstmaanTranslatorBundle(),
            new Ekino\Bundle\NewRelicBundle\EkinoNewRelicBundle(),
            new Kunstmaan\RedirectBundle\KunstmaanRedirectBundle(),
            new Kunstmaan\UserManagementBundle\KunstmaanUserManagementBundle(),
            new Kunstmaan\DashboardBundle\KunstmaanDashboardBundle(),
            //new Kunstmaan\LeadGenerationBundle\KunstmaanLeadGenerationBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev'), true)) {
            $bundles[] = new Kunstmaan\LiveReloadBundle\KunstmaanLiveReloadBundle();
            $bundles[] = new Kunstmaan\GeneratorBundle\KunstmaanGeneratorBundle();
        }

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Kunstmaan\BehatBundle\KunstmaanBehatBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
