<?php

namespace Kunstmaan\TaggingBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\Controller\AbstractAdminListController;
use Kunstmaan\TaggingBundle\AdminList\TagAdminListConfigurator;
use Kunstmaan\TaggingBundle\Entity\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TagAdminListController extends AbstractAdminListController
{
    /**
     * @var AdminListConfiguratorInterface
     */
    private $configurator;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

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
     */
    public function indexAction(Request $request): Response
    {
        return parent::doIndexAction($this->getAdminListConfigurator(), $request);
    }

    /**
     * @Route("/add", name="kunstmaantaggingbundle_admin_tag_add", methods={"GET", "POST"})
     */
    public function addAction(Request $request): Response
    {
        return parent::doAddAction($this->getAdminListConfigurator(), null, $request);
    }

    /**
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="kunstmaantaggingbundle_admin_tag_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, $id): Response
    {
        return parent::doEditAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="kunstmaantaggingbundle_admin_tag_delete", methods={"GET", "POST"})
     */
    public function deleteAction(Request $request, $id): Response
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @Route("/autocomplete.{_format}", name="kunstmaantaggingbundle_admin_tag_autocomplete", defaults={"_format" = "json"})
     */
    public function autocompleteAction(Request $request): Response
    {
        $search = $request->query->get('term');
        $qb = $this->em->getRepository(Tag::class)->createQueryBuilder('n')
            ->where('n.name LIKE :search')
            ->orderBy('n.name', 'ASC')
            ->setParameter('search', '%' . $search . '%');
        $tags = $qb->getQuery()->getResult();

        return $this->render('@KunstmaanTagging/Tags/autocomplete.json.twig', ['tags' => $tags]);
    }
}
