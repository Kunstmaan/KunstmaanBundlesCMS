<?php

namespace Kunstmaan\ViewBundle\Controller;

use Kunstmaan\AdminBundle\Entity\DynamicRoutingPageInterface;
use Kunstmaan\ViewBundle\Helper\RenderContext;
use Kunstmaan\AdminNodeBundle\Modules\NodeMenu;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Kunstmaan\AdminBundle\Modules\ClassLookup;

class SlugController extends Controller
{
    /**
     * @Route("/draft/{slug}", requirements={"slug" = ".+"}, name="_slug_draft")
     * @Template("KunstmaanViewBundle:Slug:slug.html.twig")
     */
    public function slugDraftAction($slug)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $locale = $request->getLocale();

        if (empty($locale)) {
            $locale = $request->getSession()->getLocale();
        }

        $requiredlocales = $this->container->getParameter('requiredlocales');

        $localesarray = explode('|', $requiredlocales);

        if (!empty($localesarray[0])) {
            $fallback = $localesarray[0];
        } else {
            $fallback = $this->container->getParameter('locale');
        }

        if (!in_array($locale, $localesarray)) {
            $locale = $fallback;
            return $this->redirect($this->generateUrl('_slug_draft', array('slug' => $slug, '_locale' => $locale)));
        }

        $nodeTranslation = $em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->getNodeTranslationForSlug(null, $slug);
        $exactMatch = true;
        if (!$nodeTranslation) {
            // Lookup node by best match for url
            $nodeTranslation = $em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->getBestMatchForUrl($slug, $locale);
            $exactMatch = false;
        }
        
        if ($nodeTranslation) {
            $version = $nodeTranslation->getNodeVersion('draft');
            if (is_null($version)) {
                $version = $nodeTranslation->getPublicNodeVersion();
            }
            $page = $version->getRef($em);
            $node = $nodeTranslation->getNode();
        }
        
        // If no node translation or no exact match that is not a dynamic routing page -> 404
        if (!$nodeTranslation || (!$exactMatch && !($page instanceof DynamicRoutingPageInterface))) {
            throw $this->createNotFoundException('No page found for slug ' . $slug);
        }
        
        $currentUser = $this->get('security.context')->getToken()->getUser();

        $permissionManager = $this->get('kunstmaan_admin.permissionmanager');
        $canViewPage = $permissionManager->hasPermision($node, $currentUser, 'read', $em);

        if ($canViewPage) {
            $nodeMenu = new NodeMenu($this->container, $locale, $node);

            if ($page instanceof DynamicRoutingPageInterface) {
                            $page->setLocale($locale);
                $slugPart = substr($slug, strlen($nodeTranslation->getUrl()));
                $path = $page->match($slugPart);
                if (!$path) {
                    // Try match with trailing slash - this is needed to match the root path in Controller actions...
                    $path = $page->match($slugPart . '/');
                }
                if ($path) {
                    $path['nodeTranslationId'] = $nodeTranslation->getId();
                    
                    return $this->forward($path['_controller'], $path);
                }
            }
            
            //render page
            $pageparts = array();
            if ($exactMatch && method_exists($page, 'getPagePartAdminConfigurations')) {
                foreach ($page->getPagePartAdminConfigurations() as $pagePartAdminConfiguration) {
                    $context = $pagePartAdminConfiguration->getDefaultContext();
                    $pageparts[$context] = $em->getRepository('KunstmaanPagePartBundle:PagePartRef')->getPageParts($page, $context);
                }
            }

            $renderContext = new RenderContext(
                            array('nodetranslation' => $nodeTranslation, 'slug' => $slug, 'page' => $page, 'resource' => $page, 'pageparts' => $pageparts, 'nodemenu' => $nodeMenu,
                                            'locales' => $localesarray));
            $hasView = false;
            if (method_exists($page, 'getDefaultView')) {
                $renderContext->setView($page->getDefaultView());
                $hasView = true;
            }
            if (method_exists($page, 'service')) {
                $redirect = $page->service($this->container, $request, $renderContext);
                if (!empty($redirect)) {
                    return $redirect;
                }
                else if (!$exactMatch && !$hasView) {
                    // If it was a dynamic routing page and no view and no service implementation -> 404
                    throw $this->createNotFoundException('No page found for slug ' . $slug);
                }
            }
            
            return $this->render($renderContext->getView(), (array) $renderContext);
        }
        
        throw $this->createNotFoundException('You do not have sufficient rights to access this page.');
    }

    /**
     * @Route("/")
     * @Route("/{slug}", requirements={"slug" = ".+"}, name="_slug")
     * @Template()
     */
    public function slugAction($slug = null)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $locale = $request->getLocale();

        if (empty($locale)) {
            $locale = $request->getSession()->getLocale();
        }

        $requiredlocales = $this->container->getParameter('requiredlocales');

        $localesarray = explode('|', $requiredlocales);

        if (!empty($localesarray[0])) {
            $fallback = $localesarray[0];
        } else {
            $fallback = $this->container->getParameter('locale');
        }

        if (!in_array($locale, $localesarray)) {
            $locale = $fallback;
            return $this->redirect($this->generateUrl('_slug', array('slug' => $slug, '_locale' => $locale)));
        }
        $nodeTranslation = $em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->getNodeTranslationForUrl($slug, $locale);
        $exactMatch = true;
        if (!$nodeTranslation) {
            // Lookup node by best match for url
            $nodeTranslation = $em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->getBestMatchForUrl($slug, $locale);
            $exactMatch = false;
        }
        if ($nodeTranslation) {
            $page = $nodeTranslation->getPublicNodeVersion()->getRef($em);
            $node = $nodeTranslation->getNode();
        }
        
        // If no node translation or no exact match that is not a dynamic routing page -> 404
        if (!$nodeTranslation || (!$exactMatch && !($page instanceof DynamicRoutingPageInterface))) {
            throw $this->createNotFoundException('No page found for slug ' . $slug);
        }

        //check if the requested node is online, else throw a 404 exception
        if (!$nodeTranslation->isOnline()) {
            throw $this->createNotFoundException("The requested page is not online");
        }

        $currentUser = $this->get('security.context')->getToken()->getUser();

        $permissionManager = $this->get('kunstmaan_admin.permissionmanager');
        $canViewPage = $permissionManager->hasPermision($node, $currentUser, 'read', $em);

        if ($canViewPage) {
            $nodeMenu = new NodeMenu($this->container, $locale, $node);

            if ($page instanceof DynamicRoutingPageInterface) {
                $page->setLocale($locale);
                $slugPart = substr($slug, strlen($nodeTranslation->getUrl()));
                $path = $page->match($slugPart);
                if (!$path) {
                    // Try match with trailing slash - this is needed to match the root path in Controller actions...
                    $path = $page->match($slugPart . '/');
                }
                if ($path) {
                    $path['nodeTranslationId'] = $nodeTranslation->getId();
                    
                    return $this->forward($path['_controller'], $path);
                }
            }
            
            //render page
            $pageparts = array();
            if ($exactMatch && method_exists($page, 'getPagePartAdminConfigurations')) {
                foreach ($page->getPagePartAdminConfigurations() as $pagePartAdminConfiguration) {
                    $context = $pagePartAdminConfiguration->getDefaultContext();
                    $pageparts[$context] = $em->getRepository('KunstmaanPagePartBundle:PagePartRef')->getPageParts($page, $context);
                }
            }
            $renderContext = new RenderContext(
                    array('nodetranslation' => $nodeTranslation, 'slug' => $slug, 'page' => $page, 'resource' => $page, 'pageparts' => $pageparts, 'nodemenu' => $nodeMenu,
                            'locales' => $localesarray));
            $hasView = false;
            if (method_exists($page, 'getDefaultView')) {
                $renderContext->setView($page->getDefaultView());
                $hasView = true;
            }
            if (method_exists($page, 'service')) {
                $redirect = $page->service($this->container, $request, $renderContext);
                if (!empty($redirect)) {
                    return $redirect;
                }
                else if (!$exactMatch && !$hasView) {
                    // If it was a dynamic routing page and no view and no service implementation -> 404
                    throw $this->createNotFoundException('No page found for slug ' . $slug);
                }
            }

            return $this->render($renderContext->getView(), (array) $renderContext);
        }
        
        throw $this->createNotFoundException('You do not have sufficient rights to access this page.');
    }
}
