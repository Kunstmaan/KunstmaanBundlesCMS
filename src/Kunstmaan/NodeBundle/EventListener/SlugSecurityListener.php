<?php

namespace Kunstmaan\NodeBundle\EventListener;


use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContext;
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
     * @param RequestStack $requestStack
     * @param EntityManager $entityManager
     * @param $securityContext
     * @param $acl
     */
    public function __construct(RequestStack $requestStack,EntityManager $entityManager, SecurityContext $securityContext, $acl)
    {
        $this->securityContext = $securityContext;
        $this->request = $requestStack->getCurrentRequest();
        $this->em = $entityManager;
        $this->acl = $acl;
    }

    /**
     *
     */
    public function onSlugSecurityEvent()
    {
        $node = $this->request->attributes->get('_nodeTranslation')->getNode();

        /* @var SecurityContextInterface $securityContext */
        if (false === $this->securityContext->isGranted(PermissionMap::PERMISSION_VIEW, $node)) {
            throw new AccessDeniedException('You do not have sufficient rights to access this page.');
        }

        $locale = $this->request->attributes->get('_locale');
        $preview = $this->request->attributes->get('preview');

        $nodeMenu       = new NodeMenu($this->em, $this->securityContext, $this->acl, $locale , $node, PermissionMap::PERMISSION_VIEW, $preview);

        $this->request->attributes->set('_nodeMenu', $nodeMenu);
    }
}