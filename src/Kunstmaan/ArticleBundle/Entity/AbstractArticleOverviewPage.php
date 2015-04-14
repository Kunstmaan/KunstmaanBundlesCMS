<?php

namespace Kunstmaan\ArticleBundle\Entity;

use Kunstmaan\ArticleBundle\PagePartAdmin\AbstractArticleOverviewPagePagePartAdminConfigurator;
use Kunstmaan\NodeBundle\Controller\SlugActionInterface;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * The article overview page which shows its articles
 */
abstract class AbstractArticleOverviewPage extends AbstractPage implements HasPagePartsInterface, SlugActionInterface
{
    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return array ();
    }

    /**
     * @return AbstractPagePartAdminConfigurator[]
     */
    public function getPagePartAdminConfigurations()
    {
        return array(new AbstractArticleOverviewPagePagePartAdminConfigurator());
    }

    /**
     * Return the Article repository
     *
     * @param $em
     *
     * @return mixed
     */
    abstract public function getArticleRepository($em);

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return "KunstmaanArticleBundle:AbstractArticleOverviewPage:view.html.twig";
    }

    public function getControllerAction()
    {
        return 'KunstmaanArticleBundle:AbstractArticleOverviewPage:service';
    }
}
