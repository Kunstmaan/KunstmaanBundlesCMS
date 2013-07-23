<?php

namespace Kunstmaan\PagePartBundle\Helper\Services;

use Doctrine\ORM\EntityManager;
use Kunstmaan\NodeBundle\Entity\Node,
    Kunstmaan\NodeBundle\Repository\NodeRepository,
    Kunstmaan\NodeBundle\Repository\NodeTranslationRepository;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface,
    Kunstmaan\PagePartBundle\Repository\PagePartRefRepository;

/**
 * A class to facilitate the adding of pageparts to existing pages.
 *
 * NOTE: There is a similar implementation for adding pages. See the NodeBundle for more on this.
 *
 * Class PagePartCreatorService
 * @package Kunstmaan\PagePartBundle\Helper\Services
 */
class PagePartCreatorService {

    /** @var EntityManager */
    protected $em;
    /** @var PagePartRefRepository */
    protected $pagePartRepo;
    /** @var NodeTranslationRepository */
    protected $translationRepo;
    /** @var NodeRepository */
    protected $nodeRepo;

    public function setEntityManager(EntityManager $em) {
        $this->em = $em;
        $this->pagePartRepo = $em->getRepository('KunstmaanPagePartBundle:PagePartRef');
        $this->translationRepo = $em->getRepository('KunstmaanNodeBundle:NodeTranslation');
        $this->nodeRepo = $em->getRepository('KunstmaanNodeBundle:Node');
    }
    public function getEntityManager() {
        return $this->em;
    }

    public function __construct(EntityManager $em) {
        $this->setEntityManager($em);
    }



    /**
     * Add a single pagepart to an existing page for a specific language, in an optional position.
     *
     * @param Node $node A Node instance or the internal name.
     *                   When the internal name is passed we'll get the node instance.
     *                   Based on the language we'll locate the correct Page instance.
     * @param PagePartInterface $pagePart A completely configured pagepart for this language.
     * @param $language The languagecode. nl|fr|en|.. . Just one.
     * @param string $context Where you want the pagepart to be.
     * @paral integer $position Leave null if you want to append at the end.
     *                          Otherwise set a position you would like and it'll inject
     *                          the pagepart in that position.
     *                          It won't override pageparts but it will rather inject itself in
     *                          that position and push the other pageparts down.
     */
    public function addPagePartToPage($nodeOrInternalName, PagePartInterface $pagePart, $language, $context='main', $position=null) {
        // Find the correct page instance.
        $node = $this->getNode($nodeOrInternalName);
        $translation = $node->getNodeTranslation($language);
        $page = $translation->getRef($this->em);

        // Find latest position.
        if (is_null($position)) {
            $pageparts = $this->pagePartRepo->getPagePartRefs($page, $context);
            $position = count($pageparts) + 1;
        }

        $this->em->persist($pagePart);
        $this->em->flush();

        $this->pagePartRepo->addPagePart($page, $pagePart, $position, $context);
    }

    /**
     * A helper function to more easily append multiple pageparts in different manners.
     *
     *
     * $structure is something like:
     *
     * array('main' => array(function() { return new DummyPagePart('A') }, function() { return new DummyPagePart('B') }),
     *       'banners' => array($awesomeNanner));
     *
     * So it's an array containing the pageparts per region. Each pagepart is returned by a function.
     * This is clean because we don't have to bother with variablenames which we have to remember to pass
     * to the pagecreatorservice at the right time. With this method it's impossible to assign a wrong pagepart to a page.
     * Unless you provide the incorrect page oviously ... .
     *
     * You can also include variables in the pagepart arrays if you want.
     *
     * Or optionally you can use the results of the getCreatorArgumentsForPagePartAndProperties function instead of an anonymous function.
     *
     * @param Node $node
     * @param array $structure
     * @param $language
     */
    public function addPagePartsToPage($nodeOrInternalName, array $structure, $language) {
        $node = $this->getNode($nodeOrInternalName);

        // First instantiate all pageparts. This way no pageparts will be saved if there is an issue instantiating some of them.
        $instantiatedPageparts = array();
        foreach ($structure as $context => $pageparts) {
            $instantiatedPageparts[$context] = array();

            foreach ($pageparts as $pagePartOrFunction) {
                if (is_callable($pagePartOrFunction)) {
                    $pagePartOrFunction = $pagePartOrFunction();

                    if (!isset($pagePartOrFunction) or (is_null($pagePartOrFunction))) {
                        throw new \LogicException('A function returned nothing for a pagepart. Make sure you return your instantiated pageparts in your anonymous functions.');
                    }
                }
                if (!$pagePartOrFunction instanceof PagePartInterface) {
                    throw new \LogicException('Detected a supposed pagepart that did not implement the PagePartInterface.');
                }

                $instantiatedPageparts[$context][] = $pagePartOrFunction;
            }
        }

        // All good. We can start saving.
        foreach ($instantiatedPageparts as $context => $pageparts) {
            foreach ($pageparts as $pagepart) {
                $this->addPagePartToPage($node, $pagepart, $language, $context);
            }
        }
    }

    /**
     * @param mixed(string|Node) $nodeOrInternalName
     * @return object
     */
    private function getNode($nodeOrInternalName) {
        if (is_string($nodeOrInternalName)) {
            return $this->nodeRepo->findOneBy(array('internalName' => $nodeOrInternalName));
        }
        return $nodeOrInternalName;
    }

    /**
     * A helper function to express what pagepart you want.
     *
     * It just accepts a classname and an array of setter functions with their requested values.
     *
     * It'll return an anonymous function which instantiates the pagepart.
     *
     * @param $pagePartClassName The full class name of the pagepart you want to instantiate.
     * @param array $setters An array of setternames and their values. array('setName' => 'Kim', 'isDeveloper' => true)
     */
    public function getCreatorArgumentsForPagePartAndProperties($pagePartClassName, array $setters=null) {
        return function() use ($pagePartClassName, $setters) {
            $pp = new $pagePartClassName;

            if (!is_null($setters)) {
                foreach ($setters as $setter => $value) {
                    call_user_func(array($pp, $setter), $value);
                }
            }

            return $pp;
        };
    }
}