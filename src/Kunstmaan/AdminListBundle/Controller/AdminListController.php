<?php

namespace Kunstmaan\AdminListBundle\Controller;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Event\AdaptSimpleFormEvent;
use Kunstmaan\AdminBundle\Event\Events;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\Event\AdminListEvent;
use Kunstmaan\AdminListBundle\Event\AdminListEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * AdminListController
 */
abstract class AdminListController extends Controller
{
    /**
     * You can override this method to return the correct entity manager when using multiple databases ...
     *
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * Shows the list of entities
     *
     * @param AbstractAdminListConfigurator $configurator
     * @param null|Request $request
     *
     * @return array
     */
    protected function doIndexAction(AbstractAdminListConfigurator $configurator, Request $request)
    {
        $em = $this->getEntityManager();
        /* @var AdminList $adminList */
        $adminList = $this->get("kunstmaan_adminlist.factory")->createList($configurator, $em);
        $adminList->bindRequest($request);

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
     * @return array
     */
    protected function doExportAction(AbstractAdminListConfigurator $configurator, $_format, Request $request = null)
    {
        if (!$configurator->canExport()) {
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }

        $em = $this->getEntityManager();

        /* @var AdminList $adminList */
        $adminList = $this->get("kunstmaan_adminlist.factory")->createExportList($configurator, $em);
        $adminList->bindRequest($request);

        return $this->get("kunstmaan_adminlist.service.export")->getDownloadableResponse($adminList, $_format);
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
     * @return array
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
        if (!is_object($formType) && is_string($formType)) {
            $formType = $this->container->get($formType);
        }
        $formFqn = get_class($formType);

        $event = new AdaptSimpleFormEvent($request, $formFqn, $helper, $configurator->getAdminTypeOptions());
        $event = $this->container->get('event_dispatcher')->dispatch(Events::ADAPT_SIMPLE_FORM, $event);
        $tabPane = $event->getTabPane();

        $form = $this->createForm($formFqn, $helper, $configurator->getAdminTypeOptions());

        if ($request->isMethod('POST')) {
            if ($tabPane) {
                $tabPane->bindRequest($request);
                $form = $tabPane->getForm();
            } else {
                $form->submit($request);
            }

            if ($form->isValid()) {
                $this->container->get('event_dispatcher')->dispatch(AdminListEvents::PRE_ADD, new AdminListEvent($helper));
                $em->persist($helper);
                $em->flush();
                $this->container->get('event_dispatcher')->dispatch(AdminListEvents::POST_ADD, new AdminListEvent($helper));
                $indexUrl = $configurator->getIndexUrl();

                return new RedirectResponse(
                    $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array())
                );
            }
        }

        $params = array('form' => $form->createView(), 'adminlistconfigurator' => $configurator);

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

        $formType = $configurator->getAdminType($helper);
        if (!is_object($formType) && is_string($formType)) {
            $formType = $this->container->get($formType);
        }
        $formFqn = get_class($formType);

        $event = new AdaptSimpleFormEvent($request, $formFqn, $helper, $configurator->getAdminTypeOptions());
        $event = $this->container->get('event_dispatcher')->dispatch(Events::ADAPT_SIMPLE_FORM, $event);
        $tabPane = $event->getTabPane();

        $form = $this->createForm($formFqn, $helper, $configurator->getAdminTypeOptions());

        if ($request->isMethod('POST')) {
            if ($tabPane) {
                $tabPane->bindRequest($request);
                $form = $tabPane->getForm();
            } else {
                $form->submit($request);
            }

            if ($form->isValid()) {
                $this->container->get('event_dispatcher')->dispatch(AdminListEvents::PRE_EDIT, new AdminListEvent($helper));
                $em->persist($helper);
                $em->flush();
                $this->container->get('event_dispatcher')->dispatch(AdminListEvents::POST_EDIT, new AdminListEvent($helper));
                $indexUrl = $configurator->getIndexUrl();

                return new RedirectResponse(
                    $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array())
                );
            }
        }

        $configurator->buildItemActions();

        $params = array('form' => $form->createView(), 'entity' => $helper, 'adminlistconfigurator' => $configurator);

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
            $this->container->get('event_dispatcher')->dispatch(AdminListEvents::PRE_DELETE, new AdminListEvent($helper));
            $em->remove($helper);
            $em->flush();
            $this->container->get('event_dispatcher')->dispatch(AdminListEvents::POST_DELETE, new AdminListEvent($helper));
        }

        return new RedirectResponse(
            $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array())
        );
    }
}
