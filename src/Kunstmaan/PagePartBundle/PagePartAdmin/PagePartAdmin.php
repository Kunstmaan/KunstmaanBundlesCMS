<?php

namespace Kunstmaan\PagePartBundle\PagePartAdmin;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;
use Kunstmaan\PagePartBundle\Repository\PagePartRefRepository;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

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
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $pageParts = array();

    /**
     * @var array
     */
    protected $pagePartRefs = array();

    /**
     * @var array
     */
    protected $newPageParts = array();

    /**
     * @param AbstractPagePartAdminConfigurator $configurator The configurator
     * @param EntityManager                     $em           The entity manager
     * @param HasPagePartsInterface             $page         The page
     * @param null|string                       $context      The context
     * @param null|ContainerInterface           $container    The container
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(AbstractPagePartAdminConfigurator $configurator, EntityManager $em, HasPagePartsInterface $page, $context = null, ContainerInterface $container = null)
    {
        if (!($page instanceof AbstractEntity)) {
            throw new \InvalidArgumentException("Page must be an instance of AbstractEntity.");
        }

        $this->configurator = $configurator;
        $this->em = $em;
        $this->page = $page;
        $this->container = $container;

        if ($context) {
            $this->context = $context;
        } else {
            if ($this->configurator->getContext()) {
                $this->context = $this->configurator->getContext();
            } else {
                $this->context = "main";
            }
        }

        foreach ($this->getPagePartRefs() as $pagePartRef) {
            $this->pageParts[$pagePartRef->getId()] = $this->getPagePart($pagePartRef);
            $this->pagePartRefs[$pagePartRef->getId()] = $pagePartRef;
        }
    }

    /**
     * @return AbstractEntity
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
        // Fetch all sub-entities that should be removed
        $subPagePartsToDelete = array();
        foreach (array_keys($request->request->all()) as $key) {
            // Example value: delete_pagepartadmin_74_tags_3
            if (preg_match("/^delete_pagepartadmin_(\\d+)_(\\w+)_(\\d+)$/i", $key, $matches)) {
                $subPagePartsToDelete[$matches[1]][] = array('name' => $matches[2], 'id' => $matches[3]);
            }
        }

        $doFlush = false;
        foreach ($this->pagePartRefs as $pagePartRef) {
            // Remove pageparts
            if ('true' == $request->get($pagePartRef->getId() . '_deleted')) {
                $pagePart = $this->pageParts[$pagePartRef->getId()];
                $this->em->remove($pagePart);
                $this->em->remove($pagePartRef);

                unset($this->pageParts[$pagePartRef->getId()]);
                unset($this->pagePartRefs[$pagePartRef->getId()]);
                $doFlush = true;
            }

            // Remove sub-entities from pageparts
            if (array_key_exists($pagePartRef->getId(), $subPagePartsToDelete)) {
                $pagePart = $this->pageParts[$pagePartRef->getId()];
                foreach ($subPagePartsToDelete[$pagePartRef->getId()] as $deleteInfo) {
                    $objects = call_user_func(array($pagePart, 'get' . ucfirst($deleteInfo['name'])));

                    foreach ($objects as $object) {
                        if ($object->getId() == $deleteInfo['id']) {
                            $this->em->remove($object);
                            $doFlush = true;
                        }
                    }
                }
            }
        }
        if ($doFlush) {
            $this->em->flush();
        }

        // Create the objects for the new pageparts
        $this->newPageParts = array();
        $newRefIds = $request->get($this->context . '_new');
        if (is_array($newRefIds)) {
            foreach ($newRefIds as $newId) {
                $type = $request->get($this->context . '_type_' . $newId);
                $this->newPageParts[$newId] = new $type();
            }
        }
    }

    /**
     * @param Request $request
     */
    public function bindRequest(Request $request) {}

    /**
     * @param FormBuilderInterface $formbuilder
     */
    public function adaptForm(FormBuilderInterface $formbuilder)
    {
        $data = $formbuilder->getData();

        foreach ($this->pageParts as $pagePartRefId => $pagePart) {
            $data['pagepartadmin_' . $pagePartRefId] = $pagePart;
            $adminType = $pagePart->getDefaultAdminType();
            if (!is_object($adminType) && is_string($adminType)) {
                $adminType = $this->container->get($adminType);
            }
            $formbuilder->add('pagepartadmin_' . $pagePartRefId, $adminType);
        }

        foreach ($this->newPageParts as $newPagePartRefId => $newPagePart) {
            $data['pagepartadmin_' . $newPagePartRefId] = $newPagePart;
            $adminType = $newPagePart->getDefaultAdminType();
            if (!is_object($adminType) && is_string($adminType)) {
                $adminType = $this->container->get($adminType);
            }
            $formbuilder->add('pagepartadmin_' . $newPagePartRefId, $adminType);
        }

        $formbuilder->setData($data);
    }

    /**
     * @param Request $request
     */
    public function persist(Request $request)
    {
        $newpprefs = array();
        foreach ($this->newPageParts as $newPagePartRefId => $newPagePart) {
            $this->em->persist($newPagePart);
            $this->em->flush();
            /** @var PagePartRefRepository $entityRepository  */
            $entityRepository = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef');
            $newppref = $entityRepository->addPagePart($this->page, $newPagePart, 1 /*TODO addposition*/, $this->context);
            $newpprefs[$newPagePartRefId] = $newppref;
        }

        //re-order and save pageparts
        $sequences = $request->get($this->context . '_sequence');
        for ($i = 0; $i < count($sequences); $i++) {
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

        // filter page part types that can only be added x times to the page context.
        // to achieve this, provide a 'pagelimit' parameter when adding the pp type in your PagePartAdminConfiguration
        if (!empty($possiblePPTypes)) {
            foreach ($possiblePPTypes as $possibleTypeData) {
                if (array_key_exists('pagelimit', $possibleTypeData)) {
                    $pageLimit = $possibleTypeData['pagelimit'];
                    /** @var PagePartRefRepository $entityRepository  */
                    $entityRepository = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef');
                    $formPPCount = $entityRepository->countPagePartsOfType($this->page, $possibleTypeData['class'], $this->configurator->getContext());
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
        return $this->pageParts;
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

}
