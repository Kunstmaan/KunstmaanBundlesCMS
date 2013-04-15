<?php

namespace Kunstmaan\PagePartBundle\PagePartAdmin;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;
use Kunstmaan\PagePartBundle\Repository\PagePartRefRepository;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * PagePartAdmin
 */
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
     * @var HasPagePartsInterface
     */
    protected $page;

    /**
     * @var string
     */
    protected $context;

    /**
     * @var array
     */
    protected $pagepartmap = array();

    /**
     * @var array
     */
    protected $newpps = array();

    /**
     * @param AbstractPagePartAdminConfigurator $configurator The configurator
     * @param EntityManager                     $em           The entity manager
     * @param HasPagePartsInterface             $page         The page
     * @param null|string                       $context      The context
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(AbstractPagePartAdminConfigurator $configurator, EntityManager $em, HasPagePartsInterface $page, $context = null)
    {
        if (!($page instanceof AbstractEntity)) {
            throw new \InvalidArgumentException("Page must be an instance of AbstractEntity.");
        }
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
        $this->pagepartmap = array();
        foreach ($this->getPagePartRefs() as $pagepartref) {
            $this->pagepartmap[$pagepartref->getId()] = $this->getPagePart($pagepartref);
        }
    }

    /**
     * @return \Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param Request $request
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
        $this->newpps = array();
        $newids = $request->get($this->context . "_new");
        for ($i = 0; $i < sizeof($newids); $i++) {
            $newid = $newids[$i];
            $type = $request->get($this->context . "_type_".$newid);
            $this->newpps[$newid] = new $type();
        }
        { //re-order pagepartmap
            $sequences = $request->get($this->context . "_sequence");
            if (!is_null($sequences)) {
                $this->pagepartmap = array();
                for ($i = 0; $i < sizeof($sequences); $i++) {
                    $sequence = $sequences[$i];
                    $pagepart = null;
                    if (array_key_exists($sequence, $this->newpps)) {
                        $this->pagepartmap[$sequence] = $this->newpps[$sequence];
                    } else {
                        $pagepartref = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef')->find($sequence);
                        $this->pagepartmap[$sequence] = $this->getPagePart($pagepartref);
                    }
                }
            }
        }
    }

    /**
     * @param Request $request
     */
    public function bindRequest(Request $request)
    {

    }

    /**
     * @param Request $request
     */
    public function persist(Request $request)
    {
        $newpprefs = array();
        foreach ($this->newpps as $key => $newpagepart) {
            $this->em->persist($newpagepart);
            $this->em->flush();
            /** @var PagePartRefRepository $entityRepository  */
            $entityRepository = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef');
            $newppref = $entityRepository->addPagePart($this->page, $newpagepart, 1 /*TODO addposition*/, $this->context);
            $newpprefs[$key] = $newppref;
        }
        { //re-order and save pageparts
            $sequences = $request->get($this->context . "_sequence");
            for ($i = 0; $i < sizeof($sequences); $i++) {
                $sequence = $sequences[$i];
                $pagepartref = null;
                if (array_key_exists($sequence, $newpprefs)) {
                    $pagepartref = $newpprefs[$sequence];
                } else {
                    $pagepartref = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef')->find($sequence);
                }
                if (is_object($pagepartref)) {
                    $pagepartref->setSequencenumber($i + 1);
                    $pagepartref->setContext($this->context);
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
        $result = array();

        /** @var EntityManager $em  */
        $em = $this->em;
        // filter page part types that can only be added x times to the page.
        // to achieve this, provide a 'pagelimit' parameter when adding the pp type in your PagePartAdminConfiguration
        if (!empty($possiblePPTypes)) {
            foreach ($possiblePPTypes as $possibleTypeData) {
                if (array_key_exists('pagelimit', $possibleTypeData)) {
                    $pageLimit = $possibleTypeData['pagelimit'];
                    /** @var PagePartRefRepository $entityRepository  */
                    $entityRepository = $em->getRepository('KunstmaanPagePartBundle:PagePartRef');
                    $formPPCount = $entityRepository->countPagePartsOfType($this->page, $possibleTypeData['class'], $this->configurator->getDefaultContext());
                    if ($formPPCount < $pageLimit) {
                        $result[] = $possibleTypeData;
                    }
                } else {
                    $result[] = $possibleTypeData;
                }
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->configurator->getName();
    }

    /**
     * @return array
     */
    public function getPagePartMap()
    {
        return $this->pagepartmap;
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
     * @param object $pagepart
     *
     * @return string
     */
    public function getClassName($pagepart)
    {
        return get_class($pagepart);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $formbuilder
     */
    public function adaptForm(FormBuilderInterface $formbuilder)
    {
        $pagepartrefs = $this->getPagePartRefs();
        // if (sizeof($pagepartrefs) > 0) {
        $data = $formbuilder->getData();
        for ($i = 0; $i < sizeof($pagepartrefs); $i++) {
            $pagepartref = $pagepartrefs[$i];
            $pagepart = $this->em->getRepository($pagepartref->getPagePartEntityname())->find($pagepartref->getPagePartId());
            $data['pagepartadmin_' . $pagepartref->getId()] = $pagepart;
            $formbuilder->add('pagepartadmin_' . $pagepartref->getId(), $pagepart->getDefaultAdminType());
        }
        foreach ($this->newpps as $id => $newpagepart) {
            $data['pagepartadmin_' . $id] = $newpagepart;
            $formbuilder->add('pagepartadmin_' . $id, $newpagepart->getDefaultAdminType());
        }
        $formbuilder->setData($data);
        //}
    }
}
