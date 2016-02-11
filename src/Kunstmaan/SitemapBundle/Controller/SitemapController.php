<?php

namespace Kunstmaan\SitemapBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SitemapController extends Controller
{
    /**
     * This will generate a sitemap for the specified locale.
     * Use the mode parameter to select in which mode the sitemap should be
     * generated. At this moment only XML is supported
     *
     * @Route("/sitemap-{locale}.{_format}", name="KunstmaanSitemapBundle_sitemap",
     *                                       requirements={"_format" = "xml"})
     * @Template("KunstmaanSitemapBundle:Sitemap:view.xml.twig")
     *
     * @param $locale
     *
     * @return array
     */
    public function sitemapAction($locale)
    {
        $nodeMenu = $this->get('kunstmaan_node.node_menu');
        $nodeMenu->setLocale($locale);
        $nodeMenu->setIncludeOffline(false);
        $nodeMenu->setIncludeHiddenFromNav(true);
        $nodeMenu->setCurrentNode(null);

        return array(
            'nodemenu' => $nodeMenu,
            'locale'   => $locale,
        );
    }

    /**
     * This will generate a sitemap index file to define a sub sitemap for each
     * language. Info at:
     * https://support.google.com/webmasters/answer/75712?rd=1 Use the mode
     * parameter to select in which mode the sitemap should be generated. At
     * this moment only XML is supported
     *
     * @Route("/sitemap.{_format}", name="KunstmaanSitemapBundle_sitemapindex",
     *                              requirements={"_format" = "xml"})
     * @Template("KunstmaanSitemapBundle:SitemapIndex:view.xml.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function sitemapIndexAction(Request $request)
    {
        $locales = $this->get('kunstmaan_admin.domain_configuration')
            ->getBackendLocales();

        return array(
            'locales' => $locales,
            'host'    => $request->getSchemeAndHttpHost()
        );
    }
}
