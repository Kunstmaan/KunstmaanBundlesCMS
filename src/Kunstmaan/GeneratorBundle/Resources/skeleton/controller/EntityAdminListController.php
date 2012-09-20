<?php

namespace {{ namespace }}\Controller;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Entity\Group;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Kunstmaan\FormBundle\AdminList\FormPageAdminListConfigurator;

use {{ namespace }}\AdminList\{{ entity_class }}AdminListConfigurator;
use {{ namespace }}\Form\{{ entity_class }}AdminType;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * The {{ entity_class }} admin list controller
 */
class {{ entity_class }}AdminListController extends AdminListController
{

    public function getAdminListConfiguration()
    {
        return new {{ entity_class }}AdminListConfigurator($this->getDoctrine()->getManager());
    }

    public function getAdminType()
    {
        return new {{ entity_class }}AdminType($this->container);
    }

	/**
	 * The index action
	 *
	 * @Route("/", name="{{ bundle.getName() }}_{{ entity_class }}")
	 * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
	 */
    public function indexAction()
    {
    	return parent::indexAction();
    }

    /**
     * The add action
     * 
	 * @Route("/add", name="{{ bundle.getName() }}_{{ entity_class }}_add")
	 * @Method({"GET", "POST"})
	 * @Template("KunstmaanAdminListBundle:Default:add.html.twig")
	 * @return array
	 */
    public function addAction()
    {
        return parent::addAction();
    }

    /**
     * The edit action
     * @param int $id      The entity id
     *
     * @Route("/{id}", requirements={"id" = "\d+"}, name="{{ bundle.getName() }}_{{ entity_class }}_edit")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanAdminListBundle:Default:edit.html.twig")
     *
     * @return array
     */
    public function editAction($id)
    {
    	return parent::editAction($id);
    }

    /**
     * @param integer $id
     *
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="{{ bundle.getName() }}_{{ entity_class }}_delete")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanAdminListBundle:Default:delete.html.twig")
     *
     * @return array
     */
    public function deleteAction($entity_id)
    {
        return parent::deleteAction($entity_id);
    }

    /**
     * @Route("/export.{_format}", requirements={"_format" = "csv"}, name="{{ bundle.getName() }}_{{ entity_class }}_export")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function exportAction($_format)
    {
        return parent::exportAction($_format);
	}
}
