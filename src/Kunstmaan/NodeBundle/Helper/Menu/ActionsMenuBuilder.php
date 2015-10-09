<?php

namespace Kunstmaan\NodeBundle\Helper\Menu;

use Doctrine\ORM\EntityManager;

use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\NodeBundle\Event\ConfigureActionMenuEvent;
use Kunstmaan\NodeBundle\Event\Events;

use Kunstmaan\NodeBundle\Helper\PagesConfiguration;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Knp\Menu\ItemInterface;
use Knp\Menu\FactoryInterface;

/**
 * ActionsMenuBuilder
 */
class ActionsMenuBuilder
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var NodeVersion
     */
    private $activeNodeVersion;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var SecurityContextInterface
     */
    private $context;

    /**
     * @var PagesConfiguration
     */
    private $pagesConfiguration;

    /**
     * @var bool
     */
    private $isEditableNode = true;


    /**
     * @param FactoryInterface         $factory    The factory
     * @param EntityManager            $em         The entity manager
     * @param RouterInterface          $router     The router
     * @param EventDispatcherInterface $dispatcher The event dispatcher
     * @param SecurityContextInterface $context    The security context
     * @param PagesConfiguration       $pagesConfiguration
     */
    public function __construct(
        FactoryInterface $factory,
        EntityManager $em,
        RouterInterface $router,
        EventDispatcherInterface $dispatcher,
        SecurityContextInterface $context,
        PagesConfiguration $pagesConfiguration
    ) {
        $this->factory            = $factory;
        $this->em                 = $em;
        $this->router             = $router;
        $this->dispatcher         = $dispatcher;
        $this->context            = $context;
        $this->pagesConfiguration = $pagesConfiguration;
    }

    /**
     * @return ItemInterface
     */
    public function createSubActionsMenu()
    {
        $activeNodeVersion = $this->getActiveNodeVersion();
        $menu              = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'page-sub-actions');

        if (null !== $activeNodeVersion && $this->isEditableNode) {
            $menu->addChild(
                'subaction.versions',
                array(
                    'linkAttributes' => array(
                        'data-toggle'   => 'modal',
                        'data-keyboard' => 'true',
                        'data-target'   => '#versions'
                    )
                )
            );
        }

        $this->dispatcher->dispatch(
            Events::CONFIGURE_SUB_ACTION_MENU,
            new ConfigureActionMenuEvent(
                $this->factory,
                $menu,
                $activeNodeVersion
            )
        );

        return $menu;
    }

    /**
     * @return ItemInterface
     */
    public function createActionsMenu()
    {
        $activeNodeVersion = $this->getActiveNodeVersion();
        $menu              = $this->factory->createItem('root');
        $menu->setChildrenAttribute(
            'class',
            'page-main-actions js-auto-collapse-buttons'
        );
        $menu->setChildrenAttribute(
            'data-visible-buttons',
            '3'
        );

        if (null === $activeNodeVersion) {
            $this->dispatcher->dispatch(
                Events::CONFIGURE_ACTION_MENU,
                new ConfigureActionMenuEvent(
                    $this->factory,
                    $menu,
                    $activeNodeVersion
                )
            );

            return $menu;
        }

        $activeNodeTranslation       = $activeNodeVersion->getNodeTranslation();
        $node                        = $activeNodeTranslation->getNode();
        $queuedNodeTranslationAction = $this->em->getRepository(
            'KunstmaanNodeBundle:QueuedNodeTranslationAction'
        )->findOneBy(array('nodeTranslation' => $activeNodeTranslation));

        $isFirst    = true;
        $canEdit    = $this->context->isGranted(
            PermissionMap::PERMISSION_EDIT,
            $node
        );
        $canPublish = $this->context->isGranted(
            PermissionMap::PERMISSION_PUBLISH,
            $node
        );

        if ($activeNodeVersion->isDraft() && $this->isEditableNode) {
            if ($canEdit) {
                $menu->addChild(
                    'action.saveasdraft',
                    array(
                        'linkAttributes' => array(
                            'type'  => 'submit',
                            'class' => 'js-save-btn btn btn--raise-on-hover btn-primary',
                            'value' => 'save',
                            'name'  => 'save'
                        ),
                        'extras'         => array('renderType' => 'button')
                    )
                );
                $isFirst = false;
            }

            $menu->addChild(
                'action.preview',
                array(
                    'uri'            => $this->router->generate(
                        '_slug_preview',
                        array(
                            'url'     => $activeNodeTranslation->getUrl(),
                            'version' => $activeNodeVersion->getId()
                        )
                    ),
                    'linkAttributes' => array(
                        'target' => '_blank',
                        'class'  => 'btn btn-default btn--raise-on-hover'
                    )
                )
            );

            if (empty($queuedNodeTranslationAction) && $canPublish) {
                $menu->addChild(
                    'action.publish',
                    array(
                        'linkAttributes' => array(
                            'data-toggle' => 'modal',
                            'data-target' => '#pub',
                            'class'       => 'btn btn--raise-on-hover'.($isFirst ? ' btn-primary btn-save' : ' btn-default')
                        )
                    )
                );
            }

        } else {
            if ($canEdit && $canPublish) {
                $menu->addChild(
                    'action.save',
                    array(
                        'linkAttributes' => array(
                            'type'  => 'submit',
                            'class' => 'js-save-btn btn btn--raise-on-hover btn-primary',
                            'value' => 'save',
                            'name'  => 'save'
                        ),
                        'extras'         => array('renderType' => 'button')
                    )
                );
                $isFirst = false;
            }

            if ($this->isEditableNode) {
                $menu->addChild(
                    'action.preview',
                    array(
                        'uri'            => $this->router->generate(
                            '_slug_preview',
                            array('url' => $activeNodeTranslation->getUrl())
                        ),
                        'linkAttributes' => array(
                            'target' => '_blank',
                            'class'  => 'btn btn-default btn--raise-on-hover'
                        )
                    )
                );

                if (empty($queuedNodeTranslationAction)
                    && $activeNodeTranslation->isOnline()
                    && $this->context->isGranted(
                        PermissionMap::PERMISSION_UNPUBLISH,
                        $node
                    )
                ) {
                    $menu->addChild(
                        'action.unpublish',
                        array(
                            'linkAttributes' => array(
                                'class'         => 'btn btn-default btn--raise-on-hover',
                                'data-toggle'   => 'modal',
                                'data-keyboard' => 'true',
                                'data-target'   => '#unpub'
                            )
                        )
                    );
                } elseif (empty($queuedNodeTranslationAction)
                    && !$activeNodeTranslation->isOnline()
                    && $canPublish
                ) {
                    $menu->addChild(
                        'action.publish',
                        array(
                            'linkAttributes' => array(
                                'class'         => 'btn btn-default btn--raise-on-hover',
                                'data-toggle'   => 'modal',
                                'data-keyboard' => 'true',
                                'data-target'   => '#pub'
                            )
                        )
                    );
                }

                if ($canEdit) {
                    $menu->addChild(
                        'action.saveasdraft',
                        array(
                            'linkAttributes' => array(
                                'type'  => 'submit',
                                'class' => 'btn btn--raise-on-hover'.($isFirst ? ' btn-primary btn-save' : ' btn-default'),
                                'value' => 'saveasdraft',
                                'name'  => 'saveasdraft'
                            ),
                            'extras'         => array('renderType' => 'button')
                        )
                    );
                }
            }
        }

        if ($this->pagesConfiguration->getPossibleChildTypes(
            $node->getRefEntityName()
        )
        ) {
            $menu->addChild(
                'action.addsubpage',
                array(
                    'linkAttributes' => array(
                        'type'          => 'button',
                        'class'         => 'btn btn-default btn--raise-on-hover',
                        'data-toggle'   => 'modal',
                        'data-keyboard' => 'true',
                        'data-target'   => '#add-subpage-modal'
                    ),
                    'extras'         => array('renderType' => 'button')
                )
            );
        }

        if (null !== $node->getParent() && $canEdit) {
            $menu->addChild(
                'action.duplicate',
                array(
                    'linkAttributes' => array(
                        'type'          => 'button',
                        'class'         => 'btn btn-default btn--raise-on-hover',
                        'data-toggle'   => 'modal',
                        'data-keyboard' => 'true',
                        'data-target'   => '#duplicate-page-modal'
                    ),
                    'extras'         => array('renderType' => 'button')
                )
            );
        }

        if (null !== $node->getParent()
            && $this->context->isGranted(
                PermissionMap::PERMISSION_DELETE,
                $node
            )
        ) {
            $menu->addChild(
                'action.delete',
                array(
                    'linkAttributes' => array(
                        'type'          => 'button',
                        'class'         => 'btn btn-default btn--raise-on-hover',
                        'onClick'       => 'oldEdited = isEdited; isEdited=false',
                        'data-toggle'   => 'modal',
                        'data-keyboard' => 'true',
                        'data-target'   => '#delete-page-modal'
                    ),
                    'extras'         => array('renderType' => 'button')
                )
            );
        }

        $this->dispatcher->dispatch(
            Events::CONFIGURE_ACTION_MENU,
            new ConfigureActionMenuEvent(
                $this->factory,
                $menu,
                $activeNodeVersion
            )
        );

        return $menu;
    }

    /**
     * @return ItemInterface
     */
    public function createTopActionsMenu()
    {
        $menu = $this->createActionsMenu();
        $menu->setChildrenAttribute('id', 'page-main-actions-top');
        $menu->setChildrenAttribute(
            'class',
            'page-main-actions page-main-actions--top'
        );

        return $menu;
    }

    /**
     * @return ItemInterface
     */
    public function createHomeActionsMenu()
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute(
            'class',
            'page-main-actions js-auto-collapse-buttons'
        );
        $menu->addChild(
            'action.addhomepage',
            array(
                'linkAttributes' => array(
                    'type'          => 'button',
                    'class'         => 'btn btn-default btn--raise-on-hover',
                    'data-toggle'   => 'modal',
                    'data-keyboard' => 'true',
                    'data-target'   => '#add-homepage-modal'
                ),
                'extras'         => array('renderType' => 'button')
            )
        );

        return $menu;
    }

    /**
     * @return ItemInterface
     */
    public function createTopHomeActionsMenu()
    {
        $menu = $this->createHomeActionsMenu();
        $menu->setChildrenAttribute('id', 'page-main-actions-top');
        $menu->setChildrenAttribute(
            'class',
            'page-main-actions page-main-actions--top'
        );

        return $menu;
    }

    /**
     * Set activeNodeVersion
     *
     * @param NodeVersion $activeNodeVersion
     *
     * @return ActionsMenuBuilder
     */
    public function setActiveNodeVersion(NodeVersion $activeNodeVersion)
    {
        $this->activeNodeVersion = $activeNodeVersion;

        return $this;
    }

    /**
     * Get activeNodeVersion
     *
     * @return NodeVersion
     */
    public function getActiveNodeVersion()
    {
        return $this->activeNodeVersion;
    }

    /**
     * @param boolean $value
     */
    public function setEditableNode($value)
    {
        $this->isEditableNode = $value;
    }
}
