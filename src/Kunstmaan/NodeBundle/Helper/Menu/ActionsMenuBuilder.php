<?php

namespace Kunstmaan\NodeBundle\Helper\Menu;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Event\ConfigureActionMenuEvent;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Helper\PagesConfiguration;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var PagesConfiguration
     */
    private $pagesConfiguration;

    /**
     * @var bool
     */
    private $isEditableNode = true;

    /**
     * @var bool
     */
    private $enableExportPageTemplate = true;

    /**
     * @param FactoryInterface              $factory                  The factory
     * @param EntityManager                 $em                       The entity manager
     * @param RouterInterface               $router                   The router
     * @param EventDispatcherInterface      $dispatcher               The event dispatcher
     * @param AuthorizationCheckerInterface $authorizationChecker     The security authorization checker
     * @param PagesConfiguration            $pagesConfiguration
     * @param bool                          $enableExportPageTemplate
     */
    public function __construct(
        FactoryInterface $factory,
        EntityManager $em,
        RouterInterface $router,
        EventDispatcherInterface $dispatcher,
        AuthorizationCheckerInterface $authorizationChecker,
        PagesConfiguration $pagesConfiguration,
        $enableExportPageTemplate = true
    ) {
        $this->factory = $factory;
        $this->em = $em;
        $this->router = $router;
        $this->dispatcher = $dispatcher;
        $this->authorizationChecker = $authorizationChecker;
        $this->pagesConfiguration = $pagesConfiguration;
        $this->enableExportPageTemplate = $enableExportPageTemplate;
    }

    /**
     * @return ItemInterface
     */
    public function createSubActionsMenu()
    {
        $activeNodeVersion = $this->getActiveNodeVersion();
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'page-sub-actions');

        if (null !== $activeNodeVersion && $this->isEditableNode) {
            $menu->addChild(
                'subaction.versions',
                [
                    'linkAttributes' => [
                        'data-toggle' => 'modal',
                        'data-keyboard' => 'true',
                        'data-target' => '#versions',
                    ],
                ]
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

        $translations = $activeNodeVersion->getNodeTranslation()->getNode()->getNodeTranslations(true);
        $canRecopy = false;
        foreach ($translations as $translation) {
            if ($translation->getLang() != $activeNodeVersion->getNodeTranslation()->getLang()) {
                $canRecopy = true;
            }
        }

        $menu = $this->factory->createItem('root');
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

        $activeNodeTranslation = $activeNodeVersion->getNodeTranslation();
        $node = $activeNodeTranslation->getNode();
        $queuedNodeTranslationAction = $this->em->getRepository(
            'KunstmaanNodeBundle:QueuedNodeTranslationAction'
        )->findOneBy(['nodeTranslation' => $activeNodeTranslation]);

        $isFirst = true;
        $canEdit = $this->authorizationChecker->isGranted(PermissionMap::PERMISSION_EDIT, $node);
        $canPublish = $this->authorizationChecker->isGranted(PermissionMap::PERMISSION_PUBLISH, $node);
        $isSuperAdmin = $this->authorizationChecker->isGranted(UserInterface::ROLE_SUPER_ADMIN);

        if ($activeNodeVersion->isDraft() && $this->isEditableNode) {
            if ($canEdit) {
                $menu->addChild(
                    'action.saveasdraft',
                    [
                        'linkAttributes' => [
                            'type' => 'submit',
                            'class' => 'js-save-btn btn btn--raise-on-hover btn-primary',
                            'value' => 'save',
                            'name' => 'save',
                        ],
                        'extras' => ['renderType' => 'button'],
                    ]
                );
                if ($this->enableExportPageTemplate && $isSuperAdmin && is_subclass_of($node->getRefEntityName(), HasPageTemplateInterface::class)) {
                    $menu->addChild(
                        'action.exportpagetemplate',
                        [
                            'linkAttributes' => [
                                'class' => 'btn btn-default btn--raise-on-hover',
                                'data-toggle' => 'modal',
                                'data-keyboard' => 'true',
                                'data-target' => '#exportPagetemplate',
                            ],
                        ]
                    );
                }
                if ($canRecopy) {
                    $menu->addChild(
                        'action.recopyfromlanguage',
                        [
                            'linkAttributes' => [
                                'class' => 'btn btn-default btn--raise-on-hover',
                                'data-toggle' => 'modal',
                                'data-keyboard' => 'true',
                                'data-target' => '#recopy',
                            ],
                        ]
                    );
                }
                $isFirst = false;
            }

            $menu->addChild(
                'action.preview',
                [
                    'uri' => $this->router->generate(
                        '_slug_preview',
                        [
                            'url' => $activeNodeTranslation->getUrl(),
                            'version' => $activeNodeVersion->getId(),
                        ]
                    ),
                    'linkAttributes' => [
                        'target' => '_blank',
                        'class' => 'btn btn-default btn--raise-on-hover',
                    ],
                ]
            );

            if (empty($queuedNodeTranslationAction) && $canPublish) {
                $menu->addChild(
                    'action.publish',
                    [
                        'linkAttributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#pub',
                            'class' => 'btn btn--raise-on-hover'.($isFirst ? ' btn-primary btn-save' : ' btn-default'),
                        ],
                    ]
                );
            }
        } else {
            if ($canEdit && $canPublish) {
                $menu->addChild(
                    'action.save',
                    [
                        'linkAttributes' => [
                            'type' => 'submit',
                            'class' => 'js-save-btn btn btn--raise-on-hover btn-primary',
                            'value' => 'save',
                            'name' => 'save',
                        ],
                        'extras' => ['renderType' => 'button'],
                    ]
                );
                $isFirst = false;
            }

            if ($this->isEditableNode) {
                $menu->addChild(
                    'action.preview',
                    [
                        'uri' => $this->router->generate(
                            '_slug_preview',
                            ['url' => $activeNodeTranslation->getUrl()]
                        ),
                        'linkAttributes' => [
                            'target' => '_blank',
                            'class' => 'btn btn-default btn--raise-on-hover',
                        ],
                    ]
                );

                if (empty($queuedNodeTranslationAction)
                    && $activeNodeTranslation->isOnline()
                    && $this->authorizationChecker->isGranted(
                        PermissionMap::PERMISSION_UNPUBLISH,
                        $node
                    )
                ) {
                    $menu->addChild(
                        'action.unpublish',
                        [
                            'linkAttributes' => [
                                'class' => 'btn btn-default btn--raise-on-hover',
                                'data-toggle' => 'modal',
                                'data-keyboard' => 'true',
                                'data-target' => '#unpub',
                            ],
                        ]
                    );
                } elseif (empty($queuedNodeTranslationAction)
                    && !$activeNodeTranslation->isOnline()
                    && $canPublish
                ) {
                    $menu->addChild(
                        'action.publish',
                        [
                            'linkAttributes' => [
                                'class' => 'btn btn-default btn--raise-on-hover',
                                'data-toggle' => 'modal',
                                'data-keyboard' => 'true',
                                'data-target' => '#pub',
                            ],
                        ]
                    );
                }

                if ($canEdit) {
                    $menu->addChild(
                        'action.saveasdraft',
                        [
                            'linkAttributes' => [
                                'type' => 'submit',
                                'class' => 'btn btn--raise-on-hover'.($isFirst ? ' btn-primary btn-save' : ' btn-default'),
                                'value' => 'saveasdraft',
                                'name' => 'saveasdraft',
                            ],
                            'extras' => ['renderType' => 'button'],
                        ]
                    );
                    if ($this->enableExportPageTemplate && $isSuperAdmin && is_subclass_of($node->getRefEntityName(), HasPageTemplateInterface::class)) {
                        $menu->addChild(
                            'action.exportpagetemplate',
                            [
                                'linkAttributes' => [
                                    'class' => 'btn btn-default btn--raise-on-hover',
                                    'data-toggle' => 'modal',
                                    'data-keyboard' => 'true',
                                    'data-target' => '#exportPagetemplate',
                                ],
                            ]
                        );
                    }
                    if ($canRecopy) {
                        $menu->addChild(
                            'action.recopyfromlanguage',
                            [
                                'linkAttributes' => [
                                    'class' => 'btn btn-default btn--raise-on-hover',
                                    'data-toggle' => 'modal',
                                    'data-keyboard' => 'true',
                                    'data-target' => '#recopy',
                                ],
                            ]
                        );
                    }
                }
            }
        }

        if ($this->pagesConfiguration->getPossibleChildTypes(
            $node->getRefEntityName()
        )
        ) {
            $menu->addChild(
                'action.addsubpage',
                [
                    'linkAttributes' => [
                        'type' => 'button',
                        'class' => 'btn btn-default btn--raise-on-hover',
                        'data-toggle' => 'modal',
                        'data-keyboard' => 'true',
                        'data-target' => '#add-subpage-modal',
                    ],
                    'extras' => ['renderType' => 'button'],
                ]
            );
        }

        if (null !== $node->getParent() && $canEdit) {
            $menu->addChild(
                'action.duplicate',
                [
                    'linkAttributes' => [
                        'type' => 'button',
                        'class' => 'btn btn-default btn--raise-on-hover',
                        'data-toggle' => 'modal',
                        'data-keyboard' => 'true',
                        'data-target' => '#duplicate-page-modal',
                    ],
                    'extras' => ['renderType' => 'button'],
                ]
            );
        }

        if ((null !== $node->getParent() || $node->getChildren()->isEmpty())
            && $this->authorizationChecker->isGranted(
                PermissionMap::PERMISSION_DELETE,
                $node
            )
        ) {
            $menu->addChild(
                'action.delete',
                [
                    'linkAttributes' => [
                        'type' => 'button',
                        'class' => 'btn btn-default btn--raise-on-hover',
                        'onClick' => 'oldEdited = isEdited; isEdited=false',
                        'data-toggle' => 'modal',
                        'data-keyboard' => 'true',
                        'data-target' => '#delete-page-modal',
                    ],
                    'extras' => ['renderType' => 'button'],
                ]
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
            [
                'linkAttributes' => [
                    'type' => 'button',
                    'class' => 'btn btn-default btn--raise-on-hover',
                    'data-toggle' => 'modal',
                    'data-keyboard' => 'true',
                    'data-target' => '#add-homepage-modal',
                ],
                'extras' => ['renderType' => 'button'],
            ]
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
     * @param bool $value
     */
    public function setEditableNode($value)
    {
        $this->isEditableNode = $value;
    }
}
