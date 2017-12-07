<?php

namespace Kunstmaan\AdminListBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\AdminBundle\Event\AdaptSimpleFormEvent;
use Kunstmaan\AdminBundle\Event\Events;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Traits\DependencyInjection\AdminListFactoryTrait;
use Kunstmaan\AdminBundle\Traits\DependencyInjection\ContainerTrait;
use Kunstmaan\AdminBundle\Traits\DependencyInjection\EntityManagerTrait;
use Kunstmaan\AdminBundle\Traits\DependencyInjection\EntityVersionLockTrait;
use Kunstmaan\AdminBundle\Traits\DependencyInjection\EventDispatcherTrait;
use Kunstmaan\AdminBundle\Traits\DependencyInjection\TranslatorTrait;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\AdminList\AdminListFactory;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction;
use Kunstmaan\AdminListBundle\AdminList\SortableInterface;
use Kunstmaan\AdminListBundle\Entity\LockableEntityInterface;
use Kunstmaan\AdminListBundle\Event\AdminListEvent;
use Kunstmaan\AdminListBundle\Event\AdminListEvents;
use Kunstmaan\AdminListBundle\Service\ExportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Kunstmaan\AdminListBundle\Service\EntityVersionLockService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * AdminListController
 */
abstract class AdminListController extends AbstractController
{
    use AdminListFactoryTrait,
        EventDispatcherTrait,
        EntityManagerTrait,
        EntityVersionLockTrait,
        TranslatorTrait;

    /**
     * @var ExportService
     */
    protected $exportService;

    /**
     * AdminListController constructor.
     *
     * @param EventDispatcherInterface|null $eventDispatcher
     * @param EntityManagerInterface|null   $entityManager
     * @param int                           $entityVersionLockInterval
     * @param bool                          $entityVersionLockCheck
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher = null,
        EntityManagerInterface $entityManager = null,
        AdminListFactory $adminListFactory = null,
        TranslatorInterface $translator = null,
        $entityVersionLockInterval = 15,
        $entityVersionLockCheck = false)
    {
        $this->setEventDispatcher($eventDispatcher);
        $this->setEntityManager($entityManager);
        $this->setAdminListFactory($adminListFactory);
        $this->setTranslator($translator);
        $this->setEntityVersionLockInterval($entityVersionLockInterval);
        $this->setEntityVersionLockCheck($entityVersionLockCheck);
    }

    /**
     * @return AdminListFactory
     */
    public function getExportService()
    {
        if (null !== $this->container && null === $this->exportService) {
            $this->exportService = $this->container->get("kunstmaan_adminlist.service.export");
        }

        return $this->exportService;
    }

    /**
     * @param ExportService $exportService
     */
    public function setExportService(ExportService $exportService)
    {
        if (null !== $this->container && null === $this->exportService) {
            $this->exportService = $this->container->get("kunstmaan_adminlist.service.export");
        }

        $this->exportService = $exportService;
    }

    /**
     * Shows the list of entities
     *
     * @param AbstractAdminListConfigurator $configurator
     * @param null|Request $request
     *
     * @return Response
     */
    protected function doIndexAction(AbstractAdminListConfigurator $configurator, Request $request)
    {
        $em = $this->getEntityManager();
        /* @var AdminList $adminList */
        $adminList = $this->getAdminListFactory()->createList($configurator, $em);
        $adminList->bindRequest($request);

        $this->buildSortableFieldActions($configurator);

        return new Response(
            $this->renderView(
                $configurator->getListTemplate(),
                array('adminlist' => $adminList, 'adminlistconfigurator' => $configurator, 'addparams' => array())
            )
        );
    }

    /**
     * Export a list of Entities
     *
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param string $_format The format to export to
     * @param null|Request $request
     *
     * @throws AccessDeniedHttpException
     *
     * @return Response
     */
    protected function doExportAction(AbstractAdminListConfigurator $configurator, $_format, Request $request = null)
    {
        if (!$configurator->canExport()) {
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }

        $em = $this->getEntityManager();

        /* @var AdminList $adminList */
        $adminList = $this->getAdminListFactory()->createExportList($configurator, $em);
        $adminList->bindRequest($request);

        return $this->getExportService()->getDownloadableResponse($adminList, $_format);
    }

    /**
     * Creates and processes the form to add a new Entity
     *
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param string $type The type to add
     * @param null|Request $request
     *
     * @throws AccessDeniedHttpException
     *
     * @return Response
     */
    protected function doAddAction(AbstractAdminListConfigurator $configurator, $type = null, Request $request)
    {
        if (!$configurator->canAdd()) {
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }

        /* @var EntityManager $em */
        $em = $this->getEntityManager();
        $entityName = null;
        if (isset($type)) {
            $entityName = $type;
        } else {
            $entityName = $configurator->getRepositoryName();
        }

        $classMetaData = $em->getClassMetadata($entityName);
        // Creates a new instance of the mapped class, without invoking the constructor.
        $classname = $classMetaData->getName();
        $helper = new $classname();
        $helper = $configurator->decorateNewEntity($helper);

        $formType = $configurator->getAdminType($helper);

        $event = new AdaptSimpleFormEvent($request, $formType, $helper, $configurator->getAdminTypeOptions());
        $event = $this->getEventDispatcher()->dispatch(Events::ADAPT_SIMPLE_FORM, $event);
        $tabPane = $event->getTabPane();

        $form = $this->createForm($formType, $helper, $configurator->getAdminTypeOptions());

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
                $this->getEventDispatcher()->dispatch(
                    AdminListEvents::PRE_ADD,
                    $adminListEvent
                );

                // Check if Response is given
                if ($adminListEvent->getResponse() instanceof Response) {
                    return $adminListEvent->getResponse();
                }

                // Set sort weight
                $helper = $this->setSortWeightOnNewItem($configurator, $helper);

                $em->persist($helper);
                $em->flush();
                $this->getEventDispatcher()->dispatch(
                    AdminListEvents::POST_ADD,
                    $adminListEvent
                );

                // Check if Response is given
                if ($adminListEvent->getResponse() instanceof Response) {
                    return $adminListEvent->getResponse();
                }

                $indexUrl = $configurator->getIndexUrl();

                return new RedirectResponse(
                    $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array())
                );
            }
        }

        $params = [
            'form' => $form->createView(),
            'adminlistconfigurator' => $configurator,
            'entityVersionLockCheck' => false
        ];

        if ($tabPane) {
            $params = array_merge($params, array('tabPane' => $tabPane));
        }

        return new Response(
            $this->renderView($configurator->getAddTemplate(), $params)
        );
    }

    /**
     * Creates and processes the edit form for an Entity using its ID
     *
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param string $entityId The id of the entity that will be edited
     * @param null|Request $request
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedHttpException
     *
     * @return Response
     */
    protected function doEditAction(AbstractAdminListConfigurator $configurator, $entityId, Request $request)
    {
        /* @var EntityManager $em */
        $em = $this->getEntityManager();
        $helper = $em->getRepository($configurator->getRepositoryName())->findOneById($entityId);

        if ($helper === null) {
            throw new NotFoundHttpException("Entity not found.");
        }

        if (!$configurator->canEdit($helper)) {
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }

        if ($helper instanceof LockableEntityInterface) {
            // This entity is locked
            if ($this->isLockableEntityLocked($helper)) {
                $indexUrl = $configurator->getIndexUrl();
                // Don't redirect to listing when coming from ajax request, needed for url chooser.
                if (!$request->isXmlHttpRequest()) {
                    /** @var EntityVersionLockService $entityVersionLockService*/
                    $entityVersionLockService = $this->getEntityVersionLockService();

                    $user = $entityVersionLockService->getUsersWithEntityVersionLock($helper, $this->getUser());
                    $message = $this->getTranslator()->trans('kuma_admin_list.edit.flash.locked', array('%user%' => implode(', ', $user)));
                    $this->addFlash(
                        FlashTypes::WARNING,
                        $message
                    );
                    return new RedirectResponse(
                        $this->generateUrl(
                            $indexUrl['path'],
                            isset($indexUrl['params']) ? $indexUrl['params'] : array()
                        )
                    );
                }
            }
        }

        $formType = $configurator->getAdminType($helper);

        $event = new AdaptSimpleFormEvent($request, $formType, $helper, $configurator->getAdminTypeOptions());
        $event = $this->getEventDispatcher()->dispatch(Events::ADAPT_SIMPLE_FORM, $event);
        $tabPane = $event->getTabPane();

        $form = $this->createForm($formType, $helper, $configurator->getAdminTypeOptions());

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
                $this->getEventDispatcher()->dispatch(
                    AdminListEvents::PRE_EDIT,
                    $adminListEvent
                );

                // Check if Response is given
                if ($adminListEvent->getResponse() instanceof Response) {
                    return $adminListEvent->getResponse();
                }

                $em->persist($helper);
                $em->flush();
                $this->getEventDispatcher()->dispatch(
                    AdminListEvents::POST_EDIT,
                    $adminListEvent
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
                            isset($indexUrl['params']) ? $indexUrl['params'] : array()
                        )
                    );
                }
            }
        }

        $configurator->buildItemActions();

        $params = [
            'form' => $form->createView(),
            'entity' => $helper, 'adminlistconfigurator' => $configurator,
            'entityVersionLockInterval' => $this->getEntityVersionLockInterval(),
            'entityVersionLockCheck' => $this->isEntityVersionLockCheck() && $helper instanceof LockableEntityInterface,
        ];

        if ($tabPane) {
            $params = array_merge($params, array('tabPane' => $tabPane));
        }

        return new Response(
            $this->renderView(
                $configurator->getEditTemplate(),
                $params
            )
        );
    }

    /**
     * @param AbstractAdminListConfigurator $configurator
     * @param mixed                         $entityId
     * @param Request                       $request
     *
     * @return Response
     */
    protected function doViewAction(AbstractAdminListConfigurator $configurator, $entityId, Request $request)
    {
        /* @var EntityManager $em */
        $em = $this->getEntityManager();
        $helper = $em->getRepository($configurator->getRepositoryName())->findOneById($entityId);
        if ($helper === null) {
            throw new NotFoundHttpException("Entity not found.");
        }

        if (!$configurator->canView($helper)) {
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }

        $MetaData = $em->getClassMetadata($configurator->getRepositoryName());
        $fields = array();
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($MetaData->fieldNames as $value) {
            $fields[$value] = $accessor->getValue($helper, $value);
        }


        return new Response(
            $this->renderView(
                $configurator->getViewTemplate(),
                array('entity' => $helper, 'adminlistconfigurator' => $configurator, 'fields' => $fields)
            )
        );
    }

    /**
     * Delete the Entity using its ID
     *
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param integer $entityId The id to delete
     * @param null|Request $request
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedHttpException
     *
     * @return Response
     */
    protected function doDeleteAction(AbstractAdminListConfigurator $configurator, $entityId, Request $request)
    {
        /* @var $em EntityManager */
        $em = $this->getEntityManager();
        $helper = $em->getRepository($configurator->getRepositoryName())->findOneById($entityId);
        if ($helper === null) {
            throw new NotFoundHttpException("Entity not found.");
        }
        if (!$configurator->canDelete($helper)) {
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }

        $indexUrl = $configurator->getIndexUrl();
        if ($request->isMethod('POST')) {
            $adminListEvent = new AdminListEvent($helper, $request);
            $this->getEventDispatcher()->dispatch(
                AdminListEvents::PRE_DELETE,
                $adminListEvent
            );

            // Check if Response is given
            if ($adminListEvent->getResponse() instanceof Response) {
                return $adminListEvent->getResponse();
            }

            $em->remove($helper);
            $em->flush();
            $this->getEventDispatcher()->dispatch(
                AdminListEvents::POST_DELETE,
                $adminListEvent
            );

            // Check if Response is given
            if ($adminListEvent->getResponse() instanceof Response) {
                return $adminListEvent->getResponse();
            }
        }

        return new RedirectResponse(
            $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array())
        );
    }

    /**
     * Move an item up in the list.
     *
     * @return RedirectResponse
     */
    protected function doMoveUpAction(AbstractAdminListConfigurator $configurator, $entityId, Request $request)
    {
        $em = $this->getEntityManager();
        $sortableField = $configurator->getSortableField();
        $repo = $em->getRepository($configurator->getRepositoryName());
        $item = $repo->find($entityId);

        $setter = "set".ucfirst($sortableField);
        $getter = "get".ucfirst($sortableField);

        $nextItem = $repo->createQueryBuilder('i')
            ->where('i.'.$sortableField.' < :weight')
            ->setParameter('weight', $item->$getter())
            ->orderBy('i.'.$sortableField, 'DESC')
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
            $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array())
        );
    }

    protected function doMoveDownAction(AbstractAdminListConfigurator $configurator, $entityId, Request $request)
    {
        $em = $this->getEntityManager();
        $sortableField = $configurator->getSortableField();
        $repo = $em->getRepository($configurator->getRepositoryName());
        $item = $repo->find($entityId);

        $setter = "set".ucfirst($sortableField);
        $getter = "get".ucfirst($sortableField);

        $nextItem = $repo->createQueryBuilder('i')
            ->where('i.'.$sortableField.' > :weight')
            ->setParameter('weight', $item->$getter())
            ->orderBy('i.'.$sortableField, 'ASC')
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
            $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array())
        );
    }

    /**
     * @param $repo
     * @param $sort
     *
     * @return int
     */
    private function getMaxSortableField($repo, $sort)
    {
        $maxWeight = $repo->createQueryBuilder('i')
            ->select('max(i.'.$sort.')')
            ->getQuery()
            ->getSingleScalarResult();

        return (int)$maxWeight;
    }

    /**
     * @param LockableEntityInterface $entity
     * @return bool
     */
    protected function isLockableEntityLocked(LockableEntityInterface $entity)
    {
        /** @var EntityVersionLockService $entityVersionLockService */
        $entityVersionLockService = $this->getEntityVersionLockService();

        return $entityVersionLockService->isEntityBelowThreshold($entity) || $entityVersionLockService->isEntityLocked(
                $this->getUser(),
                $entity
            );
    }

    /**
     * Sets the sort weight on a new item. Can be overridden if a non-default sorting implementation is being used.
     *
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param $item
     *
     * @return mixed
     */
    protected function setSortWeightOnNewItem(AbstractAdminListConfigurator $configurator, $item) {
        if ($configurator instanceof SortableInterface) {
            $repo = $this->getEntityManager()->getRepository($configurator->getRepositoryName());
            $sort = $configurator->getSortableField();
            $weight = $this->getMaxSortableField($repo, $sort);
            $setter = "set".ucfirst($sort);
            $item->$setter($weight + 1);
        }

        return $item;
    }

    /**
     * @param AbstractAdminListConfigurator $configurator
     */
    protected function buildSortableFieldActions(AbstractAdminListConfigurator $configurator)
    {
        // Check if Sortable interface is implemented
        if ($configurator instanceof SortableInterface) {
            $route = function (EntityInterface $item) use ($configurator) {
                return array(
                    'path' => $configurator->getPathByConvention().'_move_up',
                    'params' => array('id' => $item->getId()),
                );
            };

            $action = new SimpleItemAction($route, 'arrow-up', 'kuma_admin_list.action.move_up');
            $configurator->addItemAction($action);

            $route = function (EntityInterface $item) use ($configurator) {
                return array(
                    'path' => $configurator->getPathByConvention().'_move_down',
                    'params' => array('id' => $item->getId()),
                );
            };

            $action = new SimpleItemAction($route, 'arrow-down', 'kuma_admin_list.action.move_down');
            $configurator->addItemAction($action);
        }
    }
}
