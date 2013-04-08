<?php

namespace Kunstmaan\SearchBundle\Service;

use DoctrineExtensions\Taggable\Taggable;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\SearchBundle\Helper\IndexControllerInterface;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Sherlock\Sherlock;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class NodeSearchConfiguration implements SearchConfigurationInterface {

    private $container;
    private $em;
    private $sherlock;
    private $indexName = 'nodeindex';
    private $indexNodeType = 'page';

    public function __construct(ContainerInterface $container, $sherlock)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getEntityManager();
        $this->sherlock = $sherlock;
    }

    public function create()
    {
        $index = $this->sherlock->index($this->indexName);

        $index->mappings(
            Sherlock::mappingBuilder($this->indexNodeType)->String()->field('title'),
            Sherlock::mappingBuilder($this->indexNodeType)->String()->field('content'),
            Sherlock::mappingBuilder($this->indexNodeType)->String()->field('lang'),
            Sherlock::mappingBuilder($this->indexNodeType)->String()->field('tags')->analyzer('keyword'),
            Sherlock::mappingBuilder($this->indexNodeType)->String()->field('type')->analyzer('keyword'),
            Sherlock::mappingBuilder($this->indexNodeType)->String()->field('slug')->analyzer('keyword')
        );

        $response = $index->create();

        return $response->ok;
    }

    public function index()
    {
        $nodeRepository = $this->em->getRepository('KunstmaanNodeBundle:Node');

        $topNodes = $nodeRepository->getAllTopNodes();

        foreach($topNodes as $topNode){
            $this->indexNodeTranslations($topNode);
            $this->indexChildren($topNode);
        }
    }

    protected function indexChildren($parentNode)
    {
        foreach ($parentNode->getChildren() as $childNode) {
            $this->indexNodeTranslations($childNode);
            $this->indexChildren($childNode);
        }
    }

    protected function indexNodeTranslations($node)
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
                    ->index($this->indexName)
                    ->type($this->indexNodeType)
                    ->document($doc);
                $doc->execute();
            }
        }
    }

    public function delete()
    {
        $response = $this->sherlock->delete($this->indexName);

        return $response;
    }
}