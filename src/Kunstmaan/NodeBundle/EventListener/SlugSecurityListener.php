<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\NodeBundle\Event\SlugSecurityEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Kunstmaan\NodeBundle\Helper\NodeMenu;

/**
 * Class SlugSecurityListener
 *
 * @package Kunstmaan\NodeBundle\EventListener
 */
class SlugSecurityListener
{
    /**
     * @var SecurityContext
     */
    protected $securityContext;

    /**
     * @var EntityManager
     */
    protected $em;
    
    /**
     * @var NodeMenu
     */
    protected $nodeMenu;

    /**
     * @param EntityManager   $entityManager
     * @param SecurityContext $securityContext
     */
    public function __construct(
        EntityManager $entityManager,
        SecurityContext $securityContext,
        NodeMenu $nodeMenu
    ) {
        $this->em              = $entityManager;
        $this->securityContext = $securityContext;
        $this->nodeMenu        = $nodeMenu;
    }

    /**
     * Perform basic security checks
     *
     * @param SlugSecurityEvent $event
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function onSlugSecurityEvent(SlugSecurityEvent $event)
    {
        $node            = $event->getNode();
        $nodeTranslation = $event->getNodeTranslation();
        $request         = $event->getRequest();

        /* @var SecurityContextInterface $securityContext */
        if (false === $this->securityContext->isGranted(PermissionMap::PERMISSION_VIEW, $node)) {
            throw new AccessDeniedException(
                'You do not have sufficient rights to access this page.'
            );
        }

        $isPreview = $request->attributes->get('preview');

        if (!$isPreview && !$nodeTranslation->isOnline()) {
            throw new NotFoundHttpException('The requested page is not online');
        }
        
        $nodeMenu = $this->nodeMenu;
        $nodeMenu->setLocale($nodeTranslation->getLang());
        $nodeMenu->setCurrentNode($node);
        $nodeMenu->setIncludeOffline($isPreview);
        
        $request->attributes->set('_nodeMenu', $nodeMenu);
    }
}
