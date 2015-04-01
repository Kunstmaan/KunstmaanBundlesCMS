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
    public function __construct(
        AbstractPagePartAdminConfigurator $configurator,
        EntityManager $em,
        HasPagePartsInterface $page,
        $context = null,
        ContainerInterface $container = null
    ) {
        if (!($page instanceof AbstractEntity)) {
            throw new \InvalidArgumentException("Page must be an instance of AbstractEntity.");
        }

        $this->configurator = $configurator;
        $this->em           = $em;
        $this->page         = $page;
        $this->container    = $container;

        if ($context) {
            $this->context = $context;
        } else {
            if ($this->configurator->getContext()) {
                $this->context = $this->configurator->getContext();
            } else {
                $this->context = 'main';
            }
        }

        $this->initializePageParts();
    }

    /**
     * Get all pageparts from the database, and store them.
     */
    private function initializePageParts()
    {
        // Get all the pagepartrefs
        /** @var PagePartRefRepository $ppRefRepo */
        $ppRefRepo = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef');
        $ppRefs    = $ppRefRepo->getPagePartRefs($this->page, $this->context);

        // Group pagepartrefs per type
        $types = array();
        foreach ($ppRefs as $pagePartRef) {
            $types[$pagePartRef->getPagePartEntityname()][] = $pagePartRef->getPagePartId();
            $this->pagePartRefs[$pagePartRef->getId()]      = $pagePartRef;
        }

        // Fetch all the pageparts (only one query per pagepart type)
        $pageParts = array();
        foreach ($types as $classname => $ids) {
            $result    = $this->em->getRepository($classname)->findBy(array('id' => $ids));
            $pageParts = array_merge($pageParts, $result);
        }

        // Link the pagepartref to the pagepart
        foreach ($this->pagePartRefs as $pagePartRef) {
            foreach ($pageParts as $key => $pagePart) {
                if (ClassLookup::getClass($pagePart) == $pagePartRef->getPagePartEntityname() && $pagePart->getId(
                    ) == $pagePartRef->getPagePartId()
                ) {
                    $this->pageParts[$pagePartRef->getId()] = $pagePart;
                    unset($pageParts[$key]);
                    break;
                }
            }
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
        $newRefIds          = $request->get($this->context . '_new');

        if (is_array($newRefIds)) {
            foreach ($newRefIds as $newId) {
                $type                       = $request->get($this->context . '_type_' . $newId);
                $this->newPageParts[$newId] = new $type();
            }
        }

        // Sort pageparts again
        $sequences = $request->get($this->context . '_sequence');
        if (!is_null($sequences)) {
            $tempPageparts = $this->pageParts;
            $this->pageParts = array();
            foreach ($sequences as $sequence) {
                if (array_key_exists($sequence, $this->newPageParts)) {
                    $this->pageParts[$sequence] = $this->newPageParts[$sequence];
                } elseif (array_key_exists($sequence, $tempPageparts)) {
                    $this->pageParts[$sequence] = $tempPageparts[$sequence];
                } else
                    $this->pageParts[$sequence] = $this->getPagePart($sequence, array_search($sequence, $sequences)+1);
            }

            unset($tempPageparts);
        }
    }

    /**
     * @param Request $request
     */
    public function bindRequest(Request $request)
    {
    }

    /**
     * @param FormBuilderInterface $formbuilder
     */
    public function adaptForm(FormBuilderInterface $formbuilder)
    {
        $data = $formbuilder->getData();

        foreach ($this->pageParts as $pagePartRefId => $pagePart) {
            $data['pagepartadmin_' . $pagePartRefId] = $pagePart;
            $adminType                               = $pagePart->getDefaultAdminType();
            if (!is_object($adminType) && is_string($adminType)) {
                $adminType = $this->container->get($adminType);
            }
            $formbuilder->add('pagepartadmin_' . $pagePartRefId, $adminType);
        }

        foreach ($this->newPageParts as $newPagePartRefId => $newPagePart) {
            $data['pagepartadmin_' . $newPagePartRefId] = $newPagePart;
            $adminType                                  = $newPagePart->getDefaultAdminType();
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
        /** @var PagePartRefRepository $ppRefRepo */
        $ppRefRepo = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef');

        // Add new pageparts on the correct position + Re-order and save pageparts if needed
        $sequences = $request->get($this->context . '_sequence');
        $sequencescount = count($sequences);
        for ($i = 0; $i < $sequencescount; $i++) {
            $pagePartRefId = $sequences[$i];

            if (array_key_exists($pagePartRefId, $this->newPageParts)) {
                $newPagePart = $this->newPageParts[$pagePartRefId];
                $this->em->persist($newPagePart);
                $this->em->flush($newPagePart);

                $ppRefRepo->addPagePart($this->page, $newPagePart, ($i + 1), $this->context, false);
            } elseif (array_key_exists($pagePartRefId, $this->pagePartRefs)) {
                $pagePartRef = $this->pagePartRefs[$pagePartRefId];
                if ($pagePartRef instanceof PagePartRef && $pagePartRef->getSequencenumber() != ($i + 1)) {
                    $pagePartRef->setSequencenumber($i + 1);
                    $pagePartRef->setContext($this->context);
                    $this->em->persist($pagePartRef);
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
        $result          = array();

        // filter page part types that can only be added x times to the page context.
        // to achieve this, provide a 'pagelimit' parameter when adding the pp type in your PagePartAdminConfiguration
        if (!empty($possiblePPTypes)) {
            foreach ($possiblePPTypes as $possibleTypeData) {
                if (array_key_exists('pagelimit', $possibleTypeData)) {
                    $pageLimit = $possibleTypeData['pagelimit'];
                    /** @var PagePartRefRepository $entityRepository */
                    $entityRepository = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef');
                    $formPPCount      = $entityRepository->countPagePartsOfType(
                        $this->page,
                        $possibleTypeData['class'],
                        $this->configurator->getContext()
                    );
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
     * @param bigint $id
     * @param int    $sequenceNumber
     *
     * @return PagePart
     */
    public function getPagePart($id, $sequenceNumber)
    {
        $ppRefRepo = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef');
        return $ppRefRepo->getPagePart($id, $this->context, $sequenceNumber);
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
