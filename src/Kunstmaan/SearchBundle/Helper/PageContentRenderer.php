<?php
namespace Kunstmaan\SearchBundle\Helper;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Symfony\Component\HttpFoundation\Request;

class PageContentRenderer
{

    public function render(ContainerInterface $container, $nodetranslation, $page)
    {
        // This is a hack to get access to a new asset service when rendering the templates.
        $container->enterScope('request');
        $container->set('request', new Request(), 'request');

        // Don't bother when the parent node doesn't implement the pagepart interface.
        if (!$page instanceof HasPagePartsInterface) {
          return '';
        }

        // Get the renderer and a default view for when we use pageparts.
        $renderer = $container->get('templating');
        $view = 'KunstmaanSearchBundle:Elastica:MainElasticaForPageParts.elastica.twig';

        $em = $container->get('doctrine')->getEntityManager();
        $pageparts = $em->getRepository('KunstmaanPagePartBundle:PagePartRef')->getPageParts($page);

        return strip_tags($renderer->render($view, array('page' => $page, 'pageparts' => $pageparts, 'pagepartviewresolver' => $this)));
    }

    /*
     * Return the view to use for the pagepart.
     */
    public function getElasticaViewForPagepart($pagepart)
    {
        // If it has a method called elasticaview -> use it.
        if (method_exists($pagepart, 'getElasticaView')) {
          return $pagepart->getElasticaView();
        }

        // If not get the defaultview.
        return $pagepart->getDefaultView();
    }
}


