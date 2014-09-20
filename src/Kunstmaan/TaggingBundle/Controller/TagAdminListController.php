<?php

namespace Kunstmaan\TaggingBundle\Controller;

use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Kunstmaan\TaggingBundle\AdminList\TagAdminListConfigurator;
use Symfony\Component\HttpFoundation\Request;

class TagAdminListController extends AdminListController
{

    /**
     * @var AdminListConfiguratorInterface
     */
    private $configurator;

    /**
     * @return AdminListConfiguratorInterface
     */
    public function getAdminListConfigurator()
    {
        if (!isset($this->configurator)) {
            $this->configurator = new TagAdminListConfigurator($this->getDoctrine()->getManager());
        }

        return $this->configurator;
    }

    /**
     * @Route("/", name="KunstmaanTaggingBundle_admin_tag")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     */
    public function indexAction()
    {
        return parent::doIndexAction($this->getAdminListConfigurator());
    }

    /**
     * @Route("/add", name="KunstmaanTaggingBundle_admin_tag_add")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanAdminListBundle:Default:add.html.twig")
     * @return array
     */
    public function addAction()
    {
        return parent::doAddAction($this->getAdminListConfigurator());
    }

    /**
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="KunstmaanTaggingBundle_admin_tag_edit")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanAdminListBundle:Default:edit.html.twig")
     */
    public function editAction($id)
    {
        return parent::doEditAction($this->getAdminListConfigurator(), $id);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="KunstmaanTaggingBundle_admin_tag_delete")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanAdminListBundle:Default:delete.html.twig")
     */
    public function deleteAction($id)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id);
    }

    /**
     * @Route("/autocomplete.{_format}", name="KunstmaanTaggingBundle_admin_tag_autocomplete", defaults={"_format" = "json"})
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function autocompleteAction(Request $request)
    {
        $search = $request->get('term');
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('KunstmaanTaggingBundle:Tag')->createQueryBuilder('n')
            ->where('n.name LIKE :search')
            ->orderBy('n.name', 'ASC')
            ->setParameter('search', '%' . $search . '%');
        $tags = $qb->getQuery()->getResult();

        return array('tags' => $tags);
    }

}
