<?php

namespace Kunstmaan\NodeBundle\EventListener;


use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\NodeBundle\Event\SlugSecurityEvent;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class SlugSecurityListener
 * @package Kunstmaan\NodeBundle\EventListener
 */
class SlugSecurityListener
{
    /**
     * @var SecurityContext
     */
    protected $securityContext;

    /**
     * @var null|\Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var
     */
    protected $acl;

    /**
     * @param EntityManager $entityManager
     * @param $securityContext
     * @param $acl
     */
    public function __construct(EntityManager $entityManager, SecurityContext $securityContext, $acl)
    {
        $this->securityContext = $securityContext;
        $this->em = $entityManager;
        $this->acl = $acl;
    }

    /**
     *
     */
    public function onSlugSecurityEvent(SlugSecurityEvent $event)
    {
        $node               = $event->getNode();
        $nodeTranslation    = $event->getNodeTranslation();
        $request            = $event->getRequest();

        /* @var SecurityContextInterface $securityContext */
        if (false === $this->securityContext->isGranted(PermissionMap::PERMISSION_VIEW, $node)) {
            throw new AccessDeniedException('You do not have sufficient rights to access this page.');
        }

        $locale = $request->attributes->get('_locale');
        $preview = $request->attributes->get('preview');

        // check if the requested node is online, else throw a 404 exception (only when not previewing!)
        if (!$preview && !$nodeTranslation->isOnline()) {
            throw new NotFoundHttpException("The requested page is not online");
        }

        $nodeMenu = new NodeMenu($this->em, $this->securityContext, $this->acl, $locale , $node, PermissionMap::PERMISSION_VIEW, $preview);

        $request->attributes->set('_nodeMenu', $nodeMenu);
    }
}
