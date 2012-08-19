<?php

namespace Kunstmaan\AdminListBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

abstract class AdminListController extends Controller {

    /**
     * @return Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator
     */
    public abstract function getAdminListConfiguration();

	public abstract function getAdminType();

	/**
	 * @Route("/", name="KunstmaanAdminListBundle_admin_index")
	 * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
	 * @return array
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getEntityManager();
		$request = $this->getRequest();
        /** @var Kunstmaan\AdminListBundle\AdminList\AdminList $adminlist  */
		$adminlist = $this->get("adminlist.factory")->createList($this->getAdminListConfiguration(), $em);
		$adminlist->bindRequest($request);

		return array(
			'adminlist' => $adminlist,
		    'adminlistconfiguration' => $this->getAdminListConfiguration(),
			'addparams' => array()
		);
	}

	/**
	 * @Route("/export.{_format}", requirements={"_format" = "csv"}, name="KunstmaanAdminListBundle_admin_export")
	 * @Method({"GET", "POST"})
	 * @return array
	 */
	public function exportAction($_format) {

		$em = $this->getDoctrine()->getEntityManager();
		$request = $this->getRequest();
		/** @var Kunstmaan\AdminListBundle\AdminList\AdminList $adminlist  */
        $adminlist = $this->get("adminlist.factory")->createList($this->getAdminListConfiguration(), $em);
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
	 * @Route("/add", name="KunstmaanAdminListBundle_admin_add")
	 * @Method({"GET", "POST"})
	 * @Template("KunstmaanAdminListBundle:Default:add.html.twig")
	 * @return array
	 */
	public function addAction() {

		$em = $this->getDoctrine()->getEntityManager();
		$request = $this->getRequest();
		$entityName = $this->getAdminListConfiguration()->getRepositoryName();
		$classMetaData = $em->getClassMetadata($entityName);
		// Creates a new instance of the mapped class, without invoking the constructor.
		$helper = $classMetaData->newInstance();
		$form = $this->createForm($this->getAdminType(), $helper);

		if ('POST' == $request->getMethod()) {
			$form->bindRequest($request);
			if ($form->isValid()) {
				$em->persist($helper);
				$em->flush();
                $indexUrl = $this->getAdminListConfiguration()->getIndexUrlFor();
                return new RedirectResponse($this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array()));
			}
		}
		return array('form' => $form->createView(), 'adminlistconfiguration' => $this->getAdminListConfiguration(),);
	}

	/**
	 * @Route("/{entity_id}/edit", requirements={"entity_id" = "\d+"}, name="KunstmaanAdminListBundle_admin_edit")
	 * @Method({"GET", "POST"})
	 * @Template("KunstmaanAdminListBundle:Default:edit.html.twig")
	 * @return array
	 */
	public function editAction($entity_id) {

		$em = $this->getDoctrine()->getEntityManager();

		$request = $this->getRequest();
		$helper = $em->getRepository($this->getAdminListConfiguration()->getRepositoryName())->findOneById($entity_id);
		if ($helper == NULL) {
			throw new NotFoundHttpException("Entity not found.");
		}
		$form = $this->createForm($this->getAdminType(), $helper);

		if ('POST' == $request->getMethod()) {
			$form->bindRequest($request);
			if ($form->isValid()) {
				$em->persist($helper);
				$em->flush();
                $indexUrl = $this->getAdminListConfiguration()->getIndexUrlFor();
				return new RedirectResponse($this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array()));
			}
		}
		return array(
			'form' => $form->createView(),
		        'adminlistconfiguration' => $this->getAdminListConfiguration(),
			'entity' => $helper
		);
	}

	/**
	 * @Route("/{entity_id}/delete", requirements={"entity_id" = "\d+"}, name="KunstmaanAdminListBundle_admin_delete")
	 * @Method({"GET", "POST"})
	 * @Template("KunstmaanAdminListBundle:Default:delete.html.twig")
	 * @param integer $entity_id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|multitype:unknown \Symfony\Component\Form\FormView
	 */
	public function deleteAction($entity_id) {
		$em = $this->getDoctrine()->getEntityManager();

		$request = $this->getRequest();
		$helper = $em->getRepository($this->getAdminListConfiguration()->getRepositoryName())->findOneById($entity_id);
		if ($helper == NULL) {
			throw new NotFoundHttpException("Entity not found.");
		}
		$form = $this->createFormBuilder($helper)->add('id', "hidden")->getForm();

		if ('POST' == $request->getMethod()) {
			$form->bindRequest($request);
			if ($form->isValid()) {
				$em->remove($helper);
				$em->flush();
                $indexUrl = $this->getAdminListConfiguration()->getIndexUrlFor();
                return new RedirectResponse($this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array()));
			}
		}
		return array(
			'form' => $form->createView(),
			'entity' => $helper,
			'adminlistconfiguration' => $this->getAdminListConfiguration()
		);
	}
}
