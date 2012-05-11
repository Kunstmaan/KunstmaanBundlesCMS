<?php

namespace Kunstmaan\ViewBundle\Entity;
use Kunstmaan\AdminBundle\Entity\DeepCloneableInterface;

use Kunstmaan\AdminNodeBundle\Entity\AbstractPage;

use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\ViewBundle\Form\SearchPageAdminType;
use Kunstmaan\AdminNodeBundle\Entity\HasNode;
use Doctrine\ORM\EntityManager;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Kunstmaan\SearchBundle\Entity\SearchedFor;

/**
 * The default search page
 * 
 * @ORM\Entity
 * @ORM\Table(name="searchpage")
 * @ORM\HasLifecycleCallbacks()
 */
class SearchPage extends AbstractPage implements DeepCloneableInterface
{

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return new SearchPageAdminType();
    }

    /**
     * {@inheritdoc}
     */
    public function getPossibleChildPageTypes()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function deepClone(EntityManager $em)
    {
        $newpage = clone $this;
        $newpage->setTitle($this->getTitle());
        $em->persist($newpage);
        $em->flush();

        return $newpage;
    }

    /**
     * {@inheritdoc}
     */
    public function getPagePartAdminConfigurations()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function service($container, Request $request, &$result)
    {
        $query = $request->get("query");

        if ($query and $query != "") {
            //use the elasitica service to search for results
            $finder = $container->get('foq_elastica.finder.' . $container->getParameter('searchindexname') . '.page');

            $boolQuery = new \Elastica_Query_Bool();

            $languageQuery = new \Elastica_Query_Term(array('lang' => $request->getLocale()));
            $boolQuery->addMust($languageQuery);

            $searchQuery = new \Elastica_Query_QueryString($query);
            $searchQuery->setDefaultField('content');
            $boolQuery->addMust($searchQuery);

            $parentNode = $container->get('doctrine')->getEntityManager()->getRepository('KunstmaanAdminNodeBundle:Node')->getNodeFor($this->parent);
            if ($parentNode != null) {
                $parentQuery = new \Elastica_Query_Wildcard('parents', $parentNode->getId());
                $boolQuery->addMust($parentQuery);
            }

            $queryObj = \Elastica_Query::create($boolQuery);

            $queryObj
                    ->setHighlight(
                        array('pre_tags' => array('<em class="highlight">'), 'post_tags' => array('</em>'),
                                    'fields' => array('content' => array('fragment_size' => 200, 'number_of_fragments' => 1,),
                                            'title' => array('fragment_size' => 200, 'number_of_fragments' => 1,))));

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
        } else {
            $result['error'] = "No query given";
        }
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return "KunstmaanViewBundle:SearchPage:search.html.twig";
    }
}
