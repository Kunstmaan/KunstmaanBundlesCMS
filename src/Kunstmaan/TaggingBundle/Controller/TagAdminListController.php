<?php

namespace Kunstmaan\TaggingBundle\Controller;

use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Kunstmaan\TaggingBundle\AdminList\TagAdminListConfigurator;
use Kunstmaan\TaggingBundle\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
            $this->configurator = new TagAdminListConfigurator($this->getEntityManager());
        }

        return $this->configurator;
    }

    /**
     * @Route("/", name="kunstmaantaggingbundle_admin_tag")
     * @Template("@KunstmaanAdminList/Default/list.html.twig")
     */
    public function indexAction(Request $request)
    {
        return parent::doIndexAction($this->getAdminListConfigurator(), $request);
    }

    /**
     * @Route("/add", name="kunstmaantaggingbundle_admin_tag_add", methods={"GET", "POST"})
     * @Template("@KunstmaanAdminList/Default/add.html.twig")
     *
     * @return array
     */
    public function addAction(Request $request)
    {
        return parent::doAddAction($this->getAdminListConfigurator(), null, $request);
    }

    /**
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="kunstmaantaggingbundle_admin_tag_edit", methods={"GET", "POST"})
     * @Template("@KunstmaanAdminList/Default/edit.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        return parent::doEditAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="kunstmaantaggingbundle_admin_tag_delete", methods={"GET", "POST"})
     * @Template("@KunstmaanAdminList/Default/delete.html.twig")
     */
    public function deleteAction(Request $request, $id)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @Route("/autocomplete.{_format}", name="kunstmaantaggingbundle_admin_tag_autocomplete", defaults={"_format" = "json"})
     * @Template("@KunstmaanTagging/Tags/autocomplete.json.twig")
     *
     * @return array
     */
    public function autocompleteAction(Request $request)
    {
        $search = $request->get('term');
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository(Tag::class)->createQueryBuilder('n')
            ->where('n.name LIKE :search')
            ->orderBy('n.name', 'ASC')
            ->setParameter('search', '%' . $search . '%');
        $tags = $qb->getQuery()->getResult();

        return ['tags' => $tags];
    }
}
