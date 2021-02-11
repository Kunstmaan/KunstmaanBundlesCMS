<?php

namespace Kunstmaan\SitemapBundle\Controller;

use Kunstmaan\SitemapBundle\Event\PreSitemapRenderEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends Controller
{
    /**
     * This will generate a sitemap for the specified locale.
     * Use the mode parameter to select in which mode the sitemap should be
     * generated. At this moment only XML is supported
     *
     * @Route("/sitemap-{locale}.{_format}", name="KunstmaanSitemapBundle_sitemap",
     *                                       requirements={"_format" = "xml"})
     * @Template("@KunstmaanSitemap/Sitemap/view.xml.twig")
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

        $event = new PreSitemapRenderEvent($locale);
        $this->dispatch($event, PreSitemapRenderEvent::NAME);

        return [
            'nodemenu' => $nodeMenu,
            'locale' => $locale,
            'extraItems' => $event->getExtraItems(),
        ];
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
     * @Template("@KunstmaanSitemap/SitemapIndex/view.xml.twig")
     *
     * @return array
     */
    public function sitemapIndexAction(Request $request)
    {
        $locales = $this->get('kunstmaan_admin.domain_configuration')
            ->getBackendLocales();

        return [
            'locales' => $locales,
            'host' => $request->getSchemeAndHttpHost(),
        ];
    }

    /**
     * @param object $event
     *
     * @return object
     */
    private function dispatch($event, string $eventName)
    {
        $eventDispatcher = $this->container->get('event_dispatcher');
        if (class_exists(LegacyEventDispatcherProxy::class)) {
            $eventDispatcher = LegacyEventDispatcherProxy::decorate($eventDispatcher);

            return $eventDispatcher->dispatch($event, $eventName);
        }

        return $eventDispatcher->dispatch($eventName, $event);
    }
}
