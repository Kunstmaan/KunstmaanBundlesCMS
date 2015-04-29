<?php

namespace Kunstmaan\AdminListBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Event\AdaptSimpleFormEvent;
use Kunstmaan\AdminBundle\Event\Events;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractAdminListConfigurator;
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
     * @param null|Request                  $request
     *
     * @return array
     */
    protected function doIndexAction(AbstractAdminListConfigurator $configurator, Request $request = null)
    {
        $em = $this->getEntityManager();
        if (is_null($request)) {
            $request = $this->getRequest();
        }
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
     * @param string                        $_format      The format to export to
     * @param null|Request                  $request
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
     * @param string                        $type         The type to add
     * @param null|Request                  $request
     *
     * @throws AccessDeniedHttpException
     *
     * @return array
     */
    protected function doAddAction(AbstractAdminListConfigurator $configurator, $type = null, Request $request = null)
    {
        if (!$configurator->canAdd()) {
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }

        /* @var EntityManager $em */
        $em = $this->getEntityManager();
        if (is_null($request)) {
            $request = $this->getRequest();
        }
        $entityName = null;
        if (isset($type)) {
            $entityName = $type;
        } else {
            $entityName = $configurator->getRepositoryName();
        }

        $classMetaData = $em->getClassMetadata($entityName);
        // Creates a new instance of the mapped class, without invoking the constructor.
        $classname = $classMetaData->getName();
        $helper    = new $classname();
        $helper    = $configurator->decorateNewEntity($helper);
        $form      = $this->createForm($configurator->getAdminType($helper), $helper);

        if ($request->isMethod('POST')) {
            $form->submit($request);
            if ($form->isValid()) {
                $em->persist($helper);
                $em->flush();
                $indexUrl = $configurator->getIndexUrl();

                return new RedirectResponse(
                    $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array())
                );
            }
        }

        return new Response(
            $this->renderView(
                $configurator->getAddTemplate(),
                array('form' => $form->createView(), 'adminlistconfigurator' => $configurator)
            )
        );
    }

    /**
     * Creates and processes the edit form for an Entity using its ID
     *
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param string                        $entityId     The id of the entity that will be edited
     * @param null|Request                  $request
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedHttpException
     *
     * @return Response
     */
    protected function doEditAction(AbstractAdminListConfigurator $configurator, $entityId, Request $request = null)
    {
        /* @var EntityManager $em */
        $em = $this->getEntityManager();
        if (is_null($request)) {
            $request = $this->getRequest();
        }
        $helper = $em->getRepository($configurator->getRepositoryName())->findOneById($entityId);
        if ($helper === null) {
            throw new NotFoundHttpException("Entity not found.");
        }

        if (!$configurator->canEdit($helper)) {
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }
        $formType = $configurator->getAdminType($helper);

        $event = new AdaptSimpleFormEvent($request, $formType, $helper);
        $event = $this->container->get('event_dispatcher')->dispatch(Events::ADAPT_SIMPLE_FORM , $event);
        $tabPane = $event->getTabPane();

        $form = $this->createForm($formType , $helper);

        if ($request->isMethod('POST')) {
            if($tabPane){
                $tabPane->bindRequest($request);
                $form = $tabPane->getForm();
            } else {
                $form->submit($request);
            }

            if ($form->isValid()) {
                $em->persist($helper);
                $em->flush();
                $indexUrl = $configurator->getIndexUrl();

                return new RedirectResponse(
                    $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array())
                );
            }
        }

        $configurator->buildItemActions();

    $params =  array('form' => $form->createView(), 'entity' => $helper, 'adminlistconfigurator' => $configurator);

    if($tabPane) {
        $params = array_merge($params, array('tabPane' => $tabPane));
    }

        return new Response(
            $this->renderView(
            $configurator->getEditTemplate(), $params
            )
        );
    }

    /**
     * Delete the Entity using its ID
     *
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param integer                       $entityId     The id to delete
     * @param null|Request                  $request
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedHttpException
     *
     * @return Response
     */
    protected function doDeleteAction(AbstractAdminListConfigurator $configurator, $entityId, Request $request = null)
    {
        /* @var $em EntityManager */
        $em = $this->getEntityManager();
        if (is_null($request)) {
            $request = $this->getRequest();
        }
        $helper = $em->getRepository($configurator->getRepositoryName())->findOneById($entityId);
        if ($helper === null) {
            throw new NotFoundHttpException("Entity not found.");
        }
        if (!$configurator->canDelete($helper)) {
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }

        $indexUrl = $configurator->getIndexUrl();
        if ($request->isMethod('POST')) {
            $em->remove($helper);
            $em->flush();
        }

        return new RedirectResponse(
            $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array())
        );
    }
}
