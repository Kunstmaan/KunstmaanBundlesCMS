<?php

namespace Kunstmaan\AdminListBundle\Controller;

use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractAdminListConfigurator;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * AdminListController
 */
abstract class AdminListController extends Controller
{

    /**
     * @param AbstractAdminListConfigurator $configurator
     *
     * @return array
     */
    protected function doIndexAction(AbstractAdminListConfigurator $configurator)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        /* @var AdminList $adminlist */
        $adminlist = $this->get("kunstmaan_adminlist.factory")->createList($configurator, $em);
        $adminlist->bindRequest($request);

        return new Response($this->renderView($configurator->getListTemplate(), array('adminlist' => $adminlist, 'adminlistconfigurator' => $configurator, 'addparams' => array())));
    }

    /**
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param string                        $_format      The format to export to
     *
     * @return array
     */
    protected function doExportAction(AbstractAdminListConfigurator $configurator, $_format)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        /* @var AdminList $adminlist */
        $adminlist = $this->get("kunstmaan_adminlist.factory")->createList($configurator, $em);
        $adminlist->bindRequest($request);
        $entities = $adminlist->getItems(array());

        $response = new Response();
        $filename = sprintf('entries.%s', $_format);
        $template = sprintf("KunstmaanAdminListBundle:Default:export.%s.twig", $_format);
        $response->headers->set('Content-Type', sprintf('text/%s', $_format));
        $response->headers->set('Content-Disposition', sprintf('attachment; filename=%s', $filename));
        $response->setContent($this->renderView($template, array(
            "entities" => $entities,
            "adminlist" => $adminlist,
            "queryparams" => array()
        )));

        return $response;
    }

    /**
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param string                        $type         The type to add
     *
     * @return array
     */
    protected function doAddAction(AbstractAdminListConfigurator $configurator, $type = null)
    {
        /* @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
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
        $form = $this->createForm($configurator->getAdminType($helper), $helper);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($helper);
                $em->flush();
                $indexUrl = $configurator->getIndexUrlFor();

                return new RedirectResponse($this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array()));
            }
        }

        return new Response($this->renderView($configurator->getAddTemplate(), array('form' => $form->createView(), 'adminlistconfigurator' => $configurator)));
    }

    /**
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param string                        $entityid     The id of the entity that will be edited
     *
     * @throws NotFoundHttpException
     * @return Response
     */
    protected function doEditAction(AbstractAdminListConfigurator $configurator, $entityid)
    {
        /* @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $request = $this->getRequest();
        $helper = $em->getRepository($configurator->getRepositoryName())->findOneById($entityid);
        if ($helper == null) {
            throw new NotFoundHttpException("Entity not found.");
        }
        $form = $this->createForm($configurator->getAdminType($helper), $helper);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($helper);
                $em->flush();
                $indexUrl = $configurator->getIndexUrlFor();

                return new RedirectResponse($this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array()));
            }
        }

        $configurator->buildItemActions();

        return new Response($this->renderView($configurator->getEditTemplate(), array('form' => $form->createView(), 'entity' => $helper, 'adminlistconfigurator' => $configurator)));
    }

    /**
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param integer                       $entityid     The id to delete
     *
     * @throws NotFoundHttpException
     * @return Response
     */
    protected function doDeleteAction(AbstractAdminListConfigurator $configurator, $entityid)
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();

        $request = $this->getRequest();
        $helper = $em->getRepository($configurator->getRepositoryName())->findOneById($entityid);
        if ($helper == null) {
            throw new NotFoundHttpException("Entity not found.");
        }
        $indexUrl = $configurator->getIndexUrlFor();
        if ('POST' == $request->getMethod()) {
            $em->remove($helper);
            $em->flush();
        }

        return new RedirectResponse($this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array()));
    }
}
