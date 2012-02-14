<?php

namespace Kunstmaan\ViewBundle\Entity;

use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\ViewBundle\Form\SearchPageAdminType;
use Kunstmaan\AdminNodeBundle\Entity\HasNode;
use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\DeepCloneableIFace;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\BancontactBundle\Form\ContentPageAdminType;
use Kunstmaan\AdminBundle\Entity\PageIFace;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Kunstmaan\SearchBundle\Entity\SearchedFor;

/**
 * @ORM\Entity
 * @ORM\Table(name="searchpage")
 * @ORM\HasLifecycleCallbacks()
 */
class SearchPage implements PageIFace, DeepCloneableIFace {
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;
    protected $parent;

    public function getParent() {
		return $this->parent;
    }

    public function setParent(HasNode $parent) {
		$this->parent = $parent;
    }

    protected $possiblePermissions = array('read', 'write', 'delete');

    public function __construct() {
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
		return $this->id;
    }

    /**
     * Set id
     *
     * @param string $id
     */
    public function setId($num) {
		$this->id = $num;
    }

    public function setTitle($title) {
		$this->title = $title;
    }

    public function getTitle() {
		return $this->title;
    }

    public function __toString() {
		return $this->getTitle();
    }

    public function getDefaultAdminType() {
		return new SearchPageAdminType();
    }

    public function isOnline() {
		return true;
    }

    public function setTranslatableLocale($locale) {
		$this->locale = $locale;
    }

    public function getPossiblePermissions() {
		return $this->possiblePermissions;
    }

    public function getPossibleChildPageTypes() {
		return array();
    }

    public function deepClone(EntityManager $em) {
		$newpage = new SearchPage();
		$newpage->setTitle($this->getTitle());
		$em->persist($newpage);
		$em->flush();
		return $newpage;
    }

    public function getPagePartAdminConfigurations() {
		return array();
    }

    public function service($container, Request $request, &$result) {
		$query = $request->get("query");
		
		if($query and $query != ""){
		    //use the elasitica service to search for results
		    $finder = $container->get('foq_elastica.finder.website.page');
	
		    $boolQuery = new \Elastica_Query_Bool();
	
		    $languageQuery = new \Elastica_Query_Term(array('lang' => $request->getLocale()));
		    $boolQuery->addMust($languageQuery);
	
		    $searchQuery = new \Elastica_Query_QueryString($query);
		    $boolQuery->addMust($searchQuery);
	
		    $parentNode = $container->get('doctrine')->getEntityManager()->getRepository('KunstmaanAdminNodeBundle:Node')->getNodeFor($this->parent);
		    if($parentNode != null) {
			$parentQuery = new \Elastica_Query_Wildcard('parents', $parentNode->getId());
			$boolQuery->addMust($parentQuery);
		    }
	
		    $queryObj = \Elastica_Query::create($boolQuery);
	
		    $queryObj->setHighlight(array(
			'pre_tags' => array('<em class="highlight">'),
			'post_tags' => array('</em>'),
			'fields' => array(
				'content' => array(
					'fragment_size' => 200,
					'number_of_fragments' => 1,
				),
				'title' => array(
					'fragment_size' => 200,
					'number_of_fragments' => 1,
			    )
			)
		    ));
	
		    $pages = $finder->findPaginated($queryObj);
		    $pages->setMaxPerPage(5);
	
		    $numpage = intval($request->get('page'));
		    if (!isset($pages)) {
			$numpage = 1;
		    }
	
		    $pages->setCurrentPage($numpage);
		    
		    $searchedfor = new SearchedFor($query, $this);
		    $em = $container->get('doctrine')->getEntityManager();
		    $em->persist($searchedfor);
		    $em->flush();
	
		    $result['query'] = $query;
		    $result['results'] = $pages;
		    $result['error'] = "";
		}else{
			$result['error'] = "No query given";
		}
    }

    public function getDefaultView() {
		return "KunstmaanViewBundle:SearchPage:search.html.twig";
    }
}
