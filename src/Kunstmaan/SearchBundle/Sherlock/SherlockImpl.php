<?php

namespace Kunstmaan\SearchBundle\Sherlock;


use Doctrine\ORM\EntityManager;
use DoctrineExtensions\Taggable\Taggable;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\SearchBundle\Helper\IndexControllerInterface;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Sherlock\Sherlock;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class SherlockImpl {

    private $em;

    private $container;

    private $sherlock;

    public function __construct(ContainerInterface $container, $hostname, $port)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine')->getEntityManager();
        $this->sherlock = new Sherlock;
        $this->sherlock->addNode($hostname, $port);
    }

    public function setupIndex()
    {
        $index = $this->sherlock->index('testindex');

        $index->mappings(
            Sherlock::mappingBuilder('node')->String()->field('title'),
            Sherlock::mappingBuilder('node')->String()->field('content'),
            Sherlock::mappingBuilder('node')->String()->field('lang'),
            Sherlock::mappingBuilder('node')->String()->field('tags')->analyzer('keyword')
        );

        $response = $index->create();

        return $response->ok;
    }

    public function populateIndex()
    {
        $nodeRepository = $this->em->getRepository('KunstmaanNodeBundle:Node');

        $topNodes = $nodeRepository->getAllTopNodes();

        foreach($topNodes as $topNode){
            $this->indexNodeTranslations($topNode);
            $this->indexChildren($topNode);
        }
    }

    public function indexChildren($parentNode)
    {
        foreach ($parentNode->getChildren() as $childNode) {
            $this->indexNodeTranslations($childNode);
            $this->indexChildren($childNode);
        }
    }

    public function indexNodeTranslations($node)
    {
        foreach ($node->getNodeTranslations() as $nodeTranslation) {

            $publicNodeVersion = $nodeTranslation->getPublicNodeVersion();
            $page = $publicNodeVersion->getRef($this->em);

            if(!($page instanceof IndexControllerInterface) or $page->shouldBeIndexed()){

                $doc = array(
                    "title" => $nodeTranslation->getTitle(),
                    "lang" => $nodeTranslation->getLang(),
                    "slug"  => $nodeTranslation->getFullSlug(),
                    "type" => ClassLookup::getClassName($page),
                );

                $content = '';
                if( $page instanceof HasPagePartsInterface){
                    $this->container->enterScope('request');
                    $this->container->set('request', new Request(), 'request');
                    $pageparts = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef')->getPageParts($page);
                    $renderer = $this->container->get('templating');
                    $view = 'KunstmaanSearchBundle:PagePart:view.html.twig';
                    $content = strip_tags($renderer->render($view, array('page' => $page, 'pageparts' => $pageparts, 'pagepartviewresolver' => $this)));

                }
                $doc = array_merge($doc, array("content" => $content));
                if( $page instanceof Taggable){
                    $tags = array();
                    foreach($page->getTags() as $tag){
                        $tags[] = $tag->getName();
                    }
                    $doc = array_merge($doc, array("tags" => $tags));
                }

                $doc = $this->sherlock
                    ->document()
                    ->index('testindex')
                    ->type('node')
                    ->document($doc);
                $doc->execute();
            }
        }
    }

    public function searchIndex($querystring, $type = array(), $tags = array())
    {
        $request = $this->sherlock->search();

        $titleQuery = Sherlock::queryBuilder()->Wildcard()->field("title")->value($querystring);
        $contentQuery = Sherlock::queryBuilder()->Wildcard()->field("content")->value($querystring);

        $query = $tagQuery = Sherlock::queryBuilder()->Bool()->should($titleQuery, $contentQuery)->minimum_number_should_match(1);

        if(count($tags) > 0){
            $tagQueries = array();
            foreach($tags as $tag){
                $tagQueries[] = Sherlock::queryBuilder()->Term()->field("tags")->term($tag);
            }
            $tagQuery = Sherlock::queryBuilder()->Bool()->must($tagQueries)->minimum_number_should_match(1);
            $query = Sherlock::queryBuilder()->Bool()->must(array($tagQuery, $query))->minimum_number_should_match(1);
        }

        if($type && $type != ''){
            $typeQuery = Sherlock::queryBuilder()->Term()->field("type")->term($type);
            $query = Sherlock::queryBuilder()->Bool()->must(array($typeQuery, $query))->minimum_number_should_match(1);
        }

        $request->index("testindex")
                ->type("node")
                ->query($query);

        $tagFacet = Sherlock::facetBuilder()->Terms()->fields("tags")->facetname("tag");
        $typeFacet = Sherlock::facetBuilder()->Terms()->fields("type")->facetname("type");
        $request->facets($tagFacet, $typeFacet);

        $highlight = Sherlock::highlightBuilder()->Highlight()->pre_tags(array("<strong>"))->post_tags(array("</strong>"))->fields(array("content" => array("fragment_size" => 150, "number_of_fragments" => 1)));

        $request->highlight($highlight);

        $response = $request->execute();

        return $response;
    }

    public function deleteIndex()
    {
        $index = $this->sherlock->index('testindex');
        $response = $index->delete();

        return $response;
    }
}