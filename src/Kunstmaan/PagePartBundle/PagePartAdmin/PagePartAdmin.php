<?php

namespace Kunstmaan\PagePartBundle\PagePartAdmin;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Kunstmaan\PagePartBundle\Repository\PagePartRefRepository;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Kunstmaan\AdminNodeBundle\Entity\AbstractPage;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;

class PagePartAdmin
{
    /**
     * @var AbstractPagePartAdminConfigurator
     */
    protected $configurator;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var AbstractPage
     */
    protected $page;

    /**
     * @var string
     */
    protected $context;

    /**
     * @var AbstractPagePart[]
     */
    protected $pageparts = array();

    /**
     * @var Container
     */
    protected $container = null;

    /**
     * @param AbstractPagePartAdminConfigurator              $configurator
     * @param \Doctrine\ORM\EntityManager                    $em
     * @param \Kunstmaan\AdminNodeBundle\Entity\AbstractPage $page
     * @param null|string                                    $context
     * @param Container                                      $container
     */
    function __construct(AbstractPagePartAdminConfigurator $configurator, EntityManager $em, AbstractPage $page, $context = null, Container $container)
    {
        $this->configurator = $configurator;
        $this->em = $em;
        $this->page = $page;
        if ($context) {
            $this->context = $context;
        } else {
            if ($this->configurator->getDefaultContext()) {
                $this->context = $this->configurator->getDefaultContext();
            } else {
                $this->context = "main";
            }
        }
        $this->container = $container;
    }

    /**
     * @param $request
     */
    public function preBindRequest(Request $request)
    {
        /** @var PagePartRef[] $pagepartrefs */
        $pagepartrefs = $this->getPagePartRefs();
        { //remove pageparts
            foreach ($pagepartrefs as &$pagepartref) {
                if ("true" == $request->get($pagepartref->getId() . "_deleted")) {
                    $pagepart = $this->em->getRepository($pagepartref->getPagePartEntityname())->find($pagepartref->getPagePartId());
                    $this->em->remove($pagepart);
                    $this->em->remove($pagepartref);
                }
            }
            $this->em->flush();
        }
    }

    public function postBindRequest(Request $request)
    {
        $addpagepart = $request->request->get("addpagepart_" . $this->getContext());
        if (is_string($addpagepart) && $addpagepart != '') {
            $addpagepartposition = $request->get($this->getContext() . "_addposition");
            $newpagepart = new $addpagepart;
            $this->em->persist($newpagepart);
            $this->em->flush();
            /** @var PagePartRefRepository $entityRepository  */
            $entityRepository = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef');
            $entityRepository->addPagePart($this->page, $newpagepart, $addpagepartposition, $this->context);
        }
    }

    public function bindRequest(Request $request)
    {
        { //re-order pageparts
            $sequences = $request->get($this->context . "_" . $this->page->getId() . "_" . ClassLookup::getClass($this->page) . "_sequence");
            for ($i = 0; $i < sizeof($sequences); $i++) {
                $sequence = $sequences[$i];
                $pagepartref = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef')->find($sequence);
                if (is_object($pagepartref)) {
                    $pagepartref->setSequencenumber($i + 1);
                    $this->em->persist($pagepartref);
                }
            }
        }
    }

    /**
     * @return null|string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * This getter returns an array holding info on page part types that can be added to the page.
     * The types are filtererd here, based on the amount of page parts of a certain type that can be added to the page.
     *
     * @return array
     */
    public function getPossiblePagePartTypes()
    {
        $possiblePPTypes = $this->configurator->getPossiblePagePartTypes();
        /** @var Registry $doctrine  */
        $doctrine = $this->container->get('doctrine');
        /** @var EntityManager $em  */
        $em = $doctrine->getManager();
        // filter page part types that can only be added x times to the page.
        // to achieve this, provide a 'pagelimit' parameter when adding the pp type in your PagePartAdminConfiguration
        if (!empty($possiblePPTypes)) {
            for ($i = 0; $i < sizeof($possiblePPTypes); $i++) {
                $possibleTypeArray = $possiblePPTypes[$i];
                if (array_key_exists('pagelimit', $possibleTypeArray)) {
                    $pageLimit = $possibleTypeArray['pagelimit'];
                    /** @var PagePartRefRepository $entityRepository  */
                    $entityRepository = $em->getRepository('KunstmaanPagePartBundle:PagePartRef');
                    $formPPCount = $entityRepository->countPagePartsOfType($this->page, $possibleTypeArray['class'], $this->configurator->getDefaultContext());
                    if ($formPPCount >= $pageLimit) {
                        // 'pagelimit' reached -> remove pp type
                        unset($possiblePPTypes[$i]);
                    }
                }
            }
        }

        return $possiblePPTypes;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->configurator->getName();
    }

    /**
     * @return PagePartRef[]
     */
    public function getPagePartRefs()
    {
        $queryBuilder = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef')->createQueryBuilder('b');
        //set page and pageentityname
        $query = $queryBuilder->where('b.pageId = :pageId and b.pageEntityname = :pageEntityname and b.context = :context')
            ->setParameter('pageId', $this->page->getId())
            ->setParameter('pageEntityname', ClassLookup::getClass($this->page))
            ->setParameter('context', $this->context)->orderBy("b.sequencenumber")
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param PagePartRef $pagepartref
     *
     * @return AbstractPagePart
     */
    public function getPagePart(PagePartRef $pagepartref)
    {
        return $this->em->getRepository($pagepartref->getPagePartEntityname())->find($pagepartref->getPagePartId());
    }

    /**
     * @param AbstractPagePart $pagepart
     *
     * @return string
     */
    public function getType(AbstractPagePart $pagepart)
    {
        $possiblePagePartTypes = $this->configurator->getPossiblePagePartTypes();
        foreach ($possiblePagePartTypes as &$pageparttype) {
            if ($pageparttype['class'] == ClassLookup::getClass($pagepart)) {
                return $pageparttype['name'];
            }
        }

        return "no name";
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $formbuilder
     */
    public function adaptForm(FormBuilderInterface $formbuilder)
    {
        $pagepartrefs = $this->getPagePartRefs();
        if (sizeof($pagepartrefs) > 0) {
            $ppformbuilder = $formbuilder->getFormFactory()->createNamedBuilder('pagepartadmin_' . $this->getContext(), 'form');
            $data = $formbuilder->getData();
            for ($i = 0; $i < sizeof($pagepartrefs); $i++) {
                $pagepartref = $pagepartrefs[$i];
                $pagepart = $this->em->getRepository($pagepartref->getPagePartEntityname())->find($pagepartref->getPagePartId());
                $pageparts[] = $pagepart;
                $data['pagepartadmin_' . $this->getContext()]['pagepartadmin_' . $this->getContext() . '_' . $pagepartref->getId()] = $pagepart;
                $ppformbuilder->add('pagepartadmin_' . $this->getContext() . '_' . $pagepartref->getId(), $pagepart->getDefaultAdminType());
            }
            $formbuilder->setData($data);
            $formbuilder->add($ppformbuilder);
        }
    }
}
