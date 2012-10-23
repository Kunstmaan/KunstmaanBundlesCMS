<?php

namespace Kunstmaan\NodeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\NodeBundle\Entity\ControllerActionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\DynamicRoutingInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * This controller is for showing frontend pages based on slugs
 */
class SlugController extends Controller
{

    /**
     * Handle slug action.
     *
     * @param string $url     The url
     * @param bool   $preview Show in preview mode
     * @param bool   $draft   Show the draft version or not
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedHttpException
     * @Route("/")
     * @Route("/preview", defaults={"preview" = true})
     * @Route("/preview/{url}", requirements={"url" = ".+"}, defaults={"preview" = true}, name="_slug_preview")
     * @Route("/{url}", requirements={"url" = ".+"}, name="_slug")
     * @Template()
     *
     * @return Response|array
     */
    public function slugAction($url = null, $preview = false)
    {
        /* @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $locale = $request->getLocale();

        if (empty($locale)) {
            $locale = $request->getLocale();
        }

        $requiredLocales = $this->container->getParameter('requiredlocales');
        $localesArray = explode('|', $requiredLocales);

        if (!empty($localesArray[0])) {
            $fallback = $localesArray[0];
        } else {
            $fallback = $this->container->getParameter('locale');
        }

        if (!in_array($locale, $localesArray)) {
            if (empty($url)) {
                $url = $locale;
            } else {
                $url = $locale . '/' . $url;
            }
            $locale = $fallback;
            if ($preview) {
                return $this->redirect($this->generateUrl('_slug_preview', array('url' => $url, '_locale' => $locale)));
            } else {
                return $this->redirect($this->generateUrl('_slug', array('url' => $url, '_locale' => $locale)));
            }
        }

        /* @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $em->getRepository('KunstmaanNodeBundle:NodeTranslation')->getNodeTranslationForUrl($url, $locale);
        $exactMatch = true;
        if (!$nodeTranslation) {
            // Lookup node by best match for url
            $nodeTranslation = $em->getRepository('KunstmaanNodeBundle:NodeTranslation')->getBestMatchForUrl($url, $locale);
            $exactMatch = false;
        }

        /* @var HasNodeInterface $entity */
        $entity = null;
        $node = null;
        if ($nodeTranslation) {
            if ($preview) {
                $version = $request->get('version');
                if (!empty($version) && is_numeric($version)) {
                    $nodeVersion = $em->getRepository('KunstmaanNodeBundle:NodeVersion')->find($version);
                    if (!is_null($nodeVersion)) {
                        $entity = $nodeVersion->getRef($em);
                    }
                }
            }
            if (is_null($entity)) {
                $entity = $nodeTranslation->getPublicNodeVersion()->getRef($em);
            }
            $node = $nodeTranslation->getNode();
        }

        // If no node translation or no exact match that is not a dynamic routing page -> 404
        if (!$nodeTranslation || (!$exactMatch && !($entity instanceof DynamicRoutingInterface))) {
            throw $this->createNotFoundException('No page found for slug ' . $url);
        }

        // check if the requested node is online, else throw a 404 exception (only when not previewing!)
        if (!$preview && !$nodeTranslation->isOnline()) {
            throw $this->createNotFoundException("The requested page is not online");
        }

        /* @var SecurityContextInterface $securityContext */
        $securityContext = $this->get('security.context');
        if (false === $securityContext->isGranted(PermissionMap::PERMISSION_VIEW, $node)) {
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }

        /* @var AclHelper $aclHelper */
        $aclHelper = $this->container->get('kunstmaan_admin.acl.helper');
        $nodeMenu = new NodeMenu($em, $securityContext, $aclHelper, $locale, $node);

        if ($entity instanceof DynamicRoutingInterface) {
            /* @var DynamicRoutingInterface $entity */
            $entity->setLocale($locale);
            $slugPart = substr($url, strlen($nodeTranslation->getUrl()));
            if (false === $slugPart) {
                $slugPart = '/';
            }
            $path = $entity->match($slugPart);
            if ($path) {
                $path['nodeTranslationId'] = $nodeTranslation->getId();

                return $this->forward($path['_controller'], $path, $request->query->all());
            }
        }

        if ($entity instanceof ControllerActionInterface) {
            /* @var ControllerActionInterface $entity */
            return $this->forward($entity->getControllerAction(), $entity->getPathParams(), $entity->getQueryParams());
        }

        //render page
        $renderContext = new RenderContext(
            array(
                'nodetranslation' => $nodeTranslation,
                'slug' => $url,
                'page' => $entity,
                'resource' => $entity,
                'nodemenu' => $nodeMenu,
                'locales' => $localesArray
            )
        );
        if (method_exists($entity, 'getDefaultView')) {
            /** @noinspection PhpUndefinedMethodInspection */
            $renderContext->setView($entity->getDefaultView());
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $entity->service($this->container, $request, $renderContext);

        if ($response instanceof RedirectResponse) {
            return $response;
        }

        $view = $renderContext->getView();
        if (!empty($view)) {
            throw $this->createNotFoundException('No page found for slug ' . $url);
        }

        return $this->render($view, (array) $renderContext);
    }
}
