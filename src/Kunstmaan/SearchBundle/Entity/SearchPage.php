<?php 
namespace Kunstmaan\SearchBundle\Entity;

use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;

use Kunstmaan\SearchBundle\Form\SearchPageAdminType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="searchpage")
 */
class SearchPage extends AbstractPage implements HasPagePartsInterface
{
  public function service(ContainerInterface $container, Request $request, RenderContext $context)
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

      $parentNode = $container->get('doctrine')->getEntityManager()->getRepository('KunstmaanNodeBundle:Node')->getNodeFor($this)->getParent();
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

      $context['query'] = $query;
      $context['results'] = $pages;
      $context['error'] = "";
    } else {
      $context['error'] = "No query given";
    }
  }

    public function getPagePartAdminConfigurations()
    {
        return array();
    }

    public function getPossibleChildTypes()
    {
        return array();
    }

    public function getDefaultAdminType()
    {
        return new SearchPageAdminType();
    }

  /**
   * @return string
   */
  public function getDefaultView()
  {
    return "KunstmaanSearchBundle:SearchPage:search.html.twig";
  }

}


