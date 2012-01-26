<?php

namespace Kunstmaan\ViewBundle\Controller;

use Kunstmaan\AdminBundle\Entity\PageIFace;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Kunstmaan\AdminNodeBundle\Modules\NodeMenu;

class SearchPageController extends Controller
{
    /**
     * @Route("/search", name="_search")
     * @Template()
     */
    public function searchAction()
    {
    	$query = $this->getRequest()->get("query");
        //use the elasitica service to search for results
        $finder = $this->get('foq_elastica.finder.website.page');

        $queryObj = \Elastica_Query::create($query);
        $queryObj->setHighlight(array(
            'pre_tags' => array('<em class="highlight">'),
            'post_tags' => array('</em>'),
            'fields' => array(
                'title' => array(
                    'fragment_size' => 200,
                    'number_of_fragments' => 1,
                )
            )
        ));

        $pages = $finder->findPaginated($queryObj);

        $pages->setMaxPerPage(5);
        
        $rq = $this->getRequest();
        $page = intval($rq->get('page'));
        if(!isset($pages)){
        	$page = 1;
        }
        
        $pages->setCurrentPage($page);
                      
        $request = $this->getRequest();
        $locale = $request->getSession()->getLocale();
        $nodeMenu = new NodeMenu($this->container, $locale, null);
        
        return array(
        		'query' => $query,
        		'results' => $pages,
        		'nodemenu' => $nodeMenu
        );
    }
}
