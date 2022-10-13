<?php

namespace Kunstmaan\SitemapBundle\Controller;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\AdminBundle\Helper\EventdispatcherCompatibilityUtil;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Kunstmaan\SitemapBundle\Event\PreSitemapIndexRenderEvent;
use Kunstmaan\SitemapBundle\Event\PreSitemapRenderEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SitemapController extends AbstractController
{
    /** @var NodeMenu */
    private $nodeMenu;
    /** @var DomainConfigurationInterface */
    private $domainConfiguration;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(NodeMenu $nodeMenu, DomainConfigurationInterface $domainConfiguration, EventDispatcherInterface $eventDispatcher)
    {
        $this->nodeMenu = $nodeMenu;
        $this->domainConfiguration = $domainConfiguration;
        $this->eventDispatcher = EventdispatcherCompatibilityUtil::upgradeEventDispatcher($eventDispatcher);
    }

    /**
     * This will generate a sitemap for the specified locale.
     * Use the mode parameter to select in which mode the sitemap should be
     * generated. At this moment only XML is supported
     *
     * @Route("/sitemap-{locale}.{_format}", name="KunstmaanSitemapBundle_sitemap", requirements={"_format" = "xml"})
     */
    public function sitemapAction($locale): Response
    {
        $nodeMenu = $this->nodeMenu;
        $nodeMenu->setLocale($locale);
        $nodeMenu->setIncludeOffline(false);
        $nodeMenu->setIncludeHiddenFromNav(true);
        $nodeMenu->setCurrentNode(null);

        $event = new PreSitemapRenderEvent($locale);
        $this->eventDispatcher->dispatch($event, PreSitemapRenderEvent::NAME);

        return $this->render('@KunstmaanSitemap/Sitemap/view.xml.twig', [
            'nodemenu' => $nodeMenu,
            'locale' => $locale,
            'extraItems' => $event->getExtraItems(),
        ]);
    }

    /**
     * This will generate a sitemap index file to define a sub sitemap for each
     * language. Info at:
     * https://support.google.com/webmasters/answer/75712?rd=1 Use the mode
     * parameter to select in which mode the sitemap should be generated. At
     * this moment only XML is supported
     *
     * @Route("/sitemap.{_format}", name="KunstmaanSitemapBundle_sitemapindex", requirements={"_format" = "xml"})
     */
    public function sitemapIndexAction(Request $request): Response
    {
        $locales = $this->domainConfiguration->getBackendLocales();

        $event = new PreSitemapIndexRenderEvent($locales);
        $this->eventDispatcher->dispatch($event, PreSitemapIndexRenderEvent::NAME);

        return $this->render('@KunstmaanSitemap/SitemapIndex/view.xml.twig', [
            'locales' => $locales,
            'host' => $request->getSchemeAndHttpHost(),
            'extraSitemaps' => $event->getExtraSitemaps(),
        ]);
    }
}
