<?php

namespace Kunstmaan\AdminListBundle\Controller;

use Doctrine\ORM\EntityManager;
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
     *
     * @return array
     */
    protected function doIndexAction(AbstractAdminListConfigurator $configurator, Request $request = null)
    {
        $em = $this->getEntityManager();
        if (is_null($request)) {
            $request = $this->getRequest();
        }
        /* @var AdminList $adminlist */
        $adminlist = $this->get("kunstmaan_adminlist.factory")->createList($configurator, $em);
        $adminlist->bindRequest($request);

        return new Response(
            $this->renderView(
                $configurator->getListTemplate(),
                array('adminlist' => $adminlist, 'adminlistconfigurator' => $configurator, 'addparams' => array())
            )
        );
    }

    /**
     * Export a list of Entities
     *
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param string                        $_format      The format to export to
     *
     * @return array
     */
    protected function doExportAction(AbstractAdminListConfigurator $configurator, $_format, Request $request = null)
    {
        if (!$configurator->canExport()) {
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }

        $em = $this->getEntityManager();
        if (is_null($request)) {
            $request = $this->getRequest();
        }
        /* @var AdminList $adminlist */
        $adminlist = $this->get("kunstmaan_adminlist.factory")->createList($configurator, $em);
        $adminlist->bindRequest($request);

        return $this->get("kunstmaan_adminlist.service.export")->getDownloadableResponse($adminlist, $_format);
    }

    /**
     * Creates and processes the form to add a new Entity
     *
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param string                        $type         The type to add
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

        if ('POST' == $request->getMethod()) {
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
     *
     * @throws NotFoundHttpException
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

        $form = $this->createForm($configurator->getAdminType($helper), $helper);

        if ('POST' == $request->getMethod()) {
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

        $configurator->buildItemActions();

        return new Response(
            $this->renderView(
                $configurator->getEditTemplate(),
                array('form' => $form->createView(), 'entity' => $helper, 'adminlistconfigurator' => $configurator)
            )
        );
    }

    /**
     * Delete the Entity using its ID
     *
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param integer                       $entityId     The id to delete
     *
     * @throws NotFoundHttpException
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
        if ('POST' == $request->getMethod()) {
            $em->remove($helper);
            $em->flush();
        }

        return new RedirectResponse(
            $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array())
        );
    }
}
