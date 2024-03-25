<?php

namespace Kunstmaan\AdminListBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\AdminBundle\Event\AdaptSimpleFormEvent;
use Kunstmaan\AdminBundle\Event\Events;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\AdminList\AdminListFactory;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction;
use Kunstmaan\AdminListBundle\AdminList\SortableInterface;
use Kunstmaan\AdminListBundle\Entity\LockableEntityInterface;
use Kunstmaan\AdminListBundle\Event\AdminListEvent;
use Kunstmaan\AdminListBundle\Event\AdminListEvents;
use Kunstmaan\AdminListBundle\Service\EntityVersionLockService;
use Kunstmaan\AdminListBundle\Service\ExportService;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\UtilitiesBundle\Helper\SlugifierInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractAdminListController extends AbstractController
{
    /**
     * You can override this method to return the correct entity manager when using multiple databases ...
     *
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * Shows the list of entities
     *
     * @return Response
     */
    protected function doIndexAction(AdminListConfiguratorInterface $configurator, Request $request)
    {
        /* @var AdminList $adminList */
        $adminList = $this->container->get('kunstmaan_adminlist.factory')->createList($configurator);
        $adminList->bindRequest($request);

        $this->buildSortableFieldActions($configurator);

        return new Response(
            $this->renderView(
                $configurator->getListTemplate(),
                ['adminlist' => $adminList, 'adminlistconfigurator' => $configurator, 'addparams' => []]
            )
        );
    }

    /**
     * Export a list of Entities
     *
     * @param string $_format The format to export to
     *
     * @return Response
     *
     * @throws AccessDeniedHttpException
     */
    protected function doExportAction(AdminListConfiguratorInterface $configurator, $_format, ?Request $request = null)
    {
        if (!$configurator->canExport()) {
            throw $this->createAccessDeniedException('You do not have sufficient rights to access this page.');
        }

        /* @var AdminList $adminList */
        $adminList = $this->container->get('kunstmaan_adminlist.factory')->createExportList($configurator);
        $adminList->bindRequest($request);

        return $this->container->get('kunstmaan_adminlist.service.export')->getDownloadableResponse($adminList, $_format);
    }

    /**
     * Creates and processes the form to add a new Entity
     *
     * @param string|null $type The type to add
     *
     * @return Response
     *
     * @throws AccessDeniedHttpException
     */
    protected function doAddAction(AdminListConfiguratorInterface $configurator, $type, Request $request)
    {
        if (!$configurator->canAdd()) {
            throw $this->createAccessDeniedException('You do not have sufficient rights to access this page.');
        }

        $em = $this->getEntityManager();
        $entityName = $type ?? $configurator->getRepositoryName();

        $classMetaData = $em->getClassMetadata($entityName);
        // Creates a new instance of the mapped class, without invoking the constructor.
        $classname = $classMetaData->getName();
        $helper = new $classname();
        $helper = $configurator->decorateNewEntity($helper);

        $formType = $configurator->getAdminType($helper);

        $event = new AdaptSimpleFormEvent($request, $formType, $helper, $configurator->getAdminTypeOptions());
        $event = $this->dispatch($event, Events::ADAPT_SIMPLE_FORM);
        $tabPane = $event->getTabPane();

        $form = $this->createForm($formType, $helper, $event->getOptions());

        if ($request->isMethod('POST')) {
            if ($tabPane) {
                $tabPane->bindRequest($request);
                $form = $tabPane->getForm();
            } else {
                $form->handleRequest($request);
            }

            // Don't redirect to listing when coming from ajax request, needed for url chooser.
            if ($form->isSubmitted() && $form->isValid() && !$request->isXmlHttpRequest()) {
                $adminListEvent = new AdminListEvent($helper, $request, $form);
                $this->dispatch(
                    $adminListEvent,
                    AdminListEvents::PRE_ADD
                );

                // Check if Response is given
                if ($adminListEvent->getResponse() instanceof Response) {
                    return $adminListEvent->getResponse();
                }

                // Set sort weight
                $helper = $this->setSortWeightOnNewItem($configurator, $helper);

                $em->persist($helper);
                $em->flush();
                $this->dispatch(
                    $adminListEvent,
                    AdminListEvents::POST_ADD
                );

                // Check if Response is given
                if ($adminListEvent->getResponse() instanceof Response) {
                    return $adminListEvent->getResponse();
                }

                $indexUrl = $configurator->getIndexUrl();

                return new RedirectResponse(
                    $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : [])
                );
            }
        }

        $params = [
            'form' => $form->createView(),
            'adminlistconfigurator' => $configurator,
            'entityVersionLockCheck' => false,
            'entity' => $helper,
        ];

        if ($tabPane) {
            $params = array_merge($params, ['tabPane' => $tabPane]);
        }

        return new Response(
            $this->renderView($configurator->getAddTemplate(), $params)
        );
    }

    /**
     * Creates and processes the edit form for an Entity using its ID
     *
     * @param int|string $entityId The id of the entity that will be edited
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedHttpException
     */
    protected function doEditAction(AdminListConfiguratorInterface $configurator, $entityId, Request $request)
    {
        $em = $this->getEntityManager();
        $helper = $em->getRepository($configurator->getRepositoryName())->findOneById($entityId);

        if ($helper === null) {
            throw new NotFoundHttpException('Entity not found.');
        }

        if (!$configurator->canEdit($helper)) {
            throw $this->createAccessDeniedException('You do not have sufficient rights to access this page.');
        }

        // This entity is locked
        if (($helper instanceof LockableEntityInterface) && $this->isLockableEntityLocked($helper)) {
            $indexUrl = $configurator->getIndexUrl();
            // Don't redirect to listing when coming from ajax request, needed for url chooser.
            if (!$request->isXmlHttpRequest()) {
                /** @var EntityVersionLockService $entityVersionLockService */
                $entityVersionLockService = $this->container->get('kunstmaan_entity.admin_entity.entity_version_lock_service');

                $user = $entityVersionLockService->getUsersWithEntityVersionLock($helper, $this->getUser());
                $message = $this->container->get('translator')->trans('kuma_admin_list.edit.flash.locked', ['%user%' => implode(', ', $user)]);
                $this->addFlash(
                    FlashTypes::WARNING,
                    $message
                );

                return new RedirectResponse(
                    $this->generateUrl(
                        $indexUrl['path'],
                        isset($indexUrl['params']) ? $indexUrl['params'] : []
                    )
                );
            }
        }

        $formType = $configurator->getAdminType($helper);

        $event = new AdaptSimpleFormEvent($request, $formType, $helper, $configurator->getAdminTypeOptions());
        $event = $this->dispatch($event, Events::ADAPT_SIMPLE_FORM);
        $tabPane = $event->getTabPane();

        $form = $this->createForm($formType, $helper, $event->getOptions());

        if ($request->isMethod('POST')) {
            if ($tabPane) {
                $tabPane->bindRequest($request);
                $form = $tabPane->getForm();
            } else {
                $form->handleRequest($request);
            }

            // Don't redirect to listing when coming from ajax request, needed for url chooser.
            if ($form->isSubmitted() && $form->isValid() && !$request->isXmlHttpRequest()) {
                $adminListEvent = new AdminListEvent($helper, $request, $form);
                $this->dispatch(
                    $adminListEvent,
                    AdminListEvents::PRE_EDIT
                );

                // Check if Response is given
                if ($adminListEvent->getResponse() instanceof Response) {
                    return $adminListEvent->getResponse();
                }

                $em->persist($helper);
                $em->flush();
                $this->dispatch(
                    $adminListEvent,
                    AdminListEvents::POST_EDIT
                );

                // Check if Response is given
                if ($adminListEvent->getResponse() instanceof Response) {
                    return $adminListEvent->getResponse();
                }

                $indexUrl = $configurator->getIndexUrl();

                // Don't redirect to listing when coming from ajax request, needed for url chooser.
                if (!$request->isXmlHttpRequest()) {
                    return new RedirectResponse(
                        $this->generateUrl(
                            $indexUrl['path'],
                            isset($indexUrl['params']) ? $indexUrl['params'] : []
                        )
                    );
                }
            }
        }

        $configurator->buildItemActions();

        $params = [
            'form' => $form->createView(),
            'entity' => $helper, 'adminlistconfigurator' => $configurator,
            'entityVersionLockInterval' => $this->getParameter('kunstmaan_entity.lock_check_interval'),
            'entityVersionLockCheck' => $this->getParameter('kunstmaan_entity.lock_enabled') && $helper instanceof LockableEntityInterface,
        ];

        if ($tabPane) {
            $params = array_merge($params, ['tabPane' => $tabPane]);
        }

        return new Response(
            $this->renderView(
                $configurator->getEditTemplate(),
                $params
            )
        );
    }

    protected function doViewAction(AdminListConfiguratorInterface $configurator, $entityId, Request $request)
    {
        $em = $this->getEntityManager();
        $helper = $em->getRepository($configurator->getRepositoryName())->findOneById($entityId);
        if ($helper === null) {
            throw new NotFoundHttpException('Entity not found.');
        }

        if (!$configurator->canView($helper)) {
            throw $this->createAccessDeniedException('You do not have sufficient rights to access this page.');
        }

        $MetaData = $em->getClassMetadata($configurator->getRepositoryName());
        $fields = [];
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($MetaData->fieldNames as $value) {
            $fields[$value] = $accessor->getValue($helper, $value);
        }

        return new Response(
            $this->renderView(
                $configurator->getViewTemplate(),
                ['entity' => $helper, 'adminlistconfigurator' => $configurator, 'fields' => $fields]
            )
        );
    }

    /**
     * @param int $entityId The id to delete
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedHttpException
     */
    protected function doDeleteAction(AdminListConfiguratorInterface $configurator, $entityId, Request $request)
    {
        /** @var SlugifierInterface $slugifier */
        $slugifier = $this->container->get('kunstmaan_utilities.slugifier');

        if (!$this->isCsrfTokenValid('delete-' . $slugifier->slugify(method_exists($configurator, 'getEntityClass') ? $configurator->getEntityClass() : $configurator->getEntityName()), $request->request->get('token'))) {
            $indexUrl = $configurator->getIndexUrl();

            return new RedirectResponse($this->generateUrl($indexUrl['path'], $indexUrl['params'] ?? []));
        }

        $em = $this->getEntityManager();
        $helper = $em->getRepository($configurator->getRepositoryName())->findOneById($entityId);
        if ($helper === null) {
            throw new NotFoundHttpException('Entity not found.');
        }
        if (!$configurator->canDelete($helper)) {
            throw $this->createAccessDeniedException('You do not have sufficient rights to access this page.');
        }

        $indexUrl = $configurator->getIndexUrl();
        if ($request->isMethod('POST')) {
            $adminListEvent = new AdminListEvent($helper, $request);
            $this->dispatch(
                $adminListEvent,
                AdminListEvents::PRE_DELETE
            );

            // Check if Response is given
            if ($adminListEvent->getResponse() instanceof Response) {
                return $adminListEvent->getResponse();
            }

            $em->remove($helper);
            $em->flush();
            $this->dispatch(
                $adminListEvent,
                AdminListEvents::POST_DELETE
            );

            // Check if Response is given
            if ($adminListEvent->getResponse() instanceof Response) {
                return $adminListEvent->getResponse();
            }
        }

        return new RedirectResponse(
            $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : [])
        );
    }

    /**
     * Move an item up in the list.
     *
     * @return RedirectResponse
     */
    protected function doMoveUpAction(AdminListConfiguratorInterface $configurator, $entityId, Request $request)
    {
        $em = $this->getEntityManager();
        $sortableField = $configurator->getSortableField();

        $repositoryName = $this->getAdminListRepositoryName($configurator);

        $repo = $em->getRepository($repositoryName);
        $item = $repo->find($entityId);

        $setter = 'set' . ucfirst($sortableField);
        $getter = 'get' . ucfirst($sortableField);

        $nextItem = $repo->createQueryBuilder('i')
            ->where('i.' . $sortableField . ' < :weight')
            ->setParameter('weight', $item->$getter())
            ->orderBy('i.' . $sortableField, 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        if ($nextItem) {
            $nextItem->$setter($item->$getter());
            $em->persist($nextItem);
            $item->$setter($item->$getter() - 1);

            $em->persist($item);
            $em->flush();
        }

        $indexUrl = $configurator->getIndexUrl();

        return new RedirectResponse(
            $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : [])
        );
    }

    protected function doMoveDownAction(AdminListConfiguratorInterface $configurator, $entityId, Request $request)
    {
        $em = $this->getEntityManager();
        $sortableField = $configurator->getSortableField();

        $repositoryName = $this->getAdminListRepositoryName($configurator);

        $repo = $em->getRepository($repositoryName);
        $item = $repo->find($entityId);

        $setter = 'set' . ucfirst($sortableField);
        $getter = 'get' . ucfirst($sortableField);

        $nextItem = $repo->createQueryBuilder('i')
            ->where('i.' . $sortableField . ' > :weight')
            ->setParameter('weight', $item->$getter())
            ->orderBy('i.' . $sortableField, 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        if ($nextItem) {
            $nextItem->$setter($item->$getter());
            $em->persist($nextItem);
            $item->$setter($item->$getter() + 1);

            $em->persist($item);
            $em->flush();
        }

        $indexUrl = $configurator->getIndexUrl();

        return new RedirectResponse(
            $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : [])
        );
    }

    private function getMaxSortableField($repo, $sort)
    {
        $maxWeight = $repo->createQueryBuilder('i')
            ->select('max(i.' . $sort . ')')
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $maxWeight;
    }

    /**
     * @return bool
     */
    protected function isLockableEntityLocked(LockableEntityInterface $entity)
    {
        /** @var EntityVersionLockService $entityVersionLockService */
        $entityVersionLockService = $this->container->get('kunstmaan_entity.admin_entity.entity_version_lock_service');

        return $entityVersionLockService->isEntityBelowThreshold($entity) && $entityVersionLockService->isEntityLocked(
            $this->getUser(),
            $entity
        );
    }

    /**
     * Sets the sort weight on a new item. Can be overridden if a non-default sorting implementation is being used.
     */
    protected function setSortWeightOnNewItem(AdminListConfiguratorInterface $configurator, $item)
    {
        if ($configurator instanceof SortableInterface) {
            $repo = $this->getEntityManager()->getRepository($configurator->getRepositoryName());
            $sort = $configurator->getSortableField();
            $weight = $this->getMaxSortableField($repo, $sort);
            $setter = 'set' . ucfirst($sort);
            $item->$setter($weight + 1);
        }

        return $item;
    }

    protected function buildSortableFieldActions(AdminListConfiguratorInterface $configurator)
    {
        // Check if Sortable interface is implemented
        if ($configurator instanceof SortableInterface) {
            $route = function (EntityInterface $item) use ($configurator) {
                return [
                    'path' => $configurator->getPathByConvention() . '_move_up',
                    'params' => ['id' => $item->getId()],
                ];
            };

            $action = new SimpleItemAction($route, 'arrow-up', 'kuma_admin_list.action.move_up');
            $configurator->addItemAction($action);

            $route = function (EntityInterface $item) use ($configurator) {
                return [
                    'path' => $configurator->getPathByConvention() . '_move_down',
                    'params' => ['id' => $item->getId()],
                ];
            };

            $action = new SimpleItemAction($route, 'arrow-down', 'kuma_admin_list.action.move_down');
            $configurator->addItemAction($action);
        }
    }

    /**
     * @return string
     */
    protected function getAdminListRepositoryName(AdminListConfiguratorInterface $configurator)
    {
        $em = $this->getEntityManager();
        $className = $em->getClassMetadata($configurator->getRepositoryName())->getName();

        $implements = class_implements($className);
        if (isset($implements[HasNodeInterface::class])) {
            return NodeTranslation::class;
        }

        return $configurator->getRepositoryName();
    }

    /**
     * @param object $event
     */
    private function dispatch($event, string $eventName): object
    {
        return $this->container->get('event_dispatcher')->dispatch($event, $eventName);
    }

    public static function getSubscribedServices(): array
    {
        return [
            'doctrine' => ManagerRegistry::class,
            'doctrine.orm.entity_manager' => EntityManagerInterface::class,
            'kunstmaan_adminlist.factory' => AdminListFactory::class,
            'kunstmaan_adminlist.service.export' => ExportService::class,
            'kunstmaan_entity.admin_entity.entity_version_lock_service' => EntityVersionLockService::class,
            'translator' => TranslatorInterface::class,
            'event_dispatcher' => EventDispatcherInterface::class,
            'kunstmaan_utilities.slugifier' => SlugifierInterface::class,
        ] + parent::getSubscribedServices();
    }
}
