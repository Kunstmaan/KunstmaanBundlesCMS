<?php

namespace Kunstmaan\SearchBundle\Service;

use DoctrineExtensions\Taggable\Taggable;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\SearchBundle\Helper\IndexControllerInterface;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class NodeIndexer implements IndexerInterface {

    private $container;
    private $em;
    private $sherlock;
    private $indexName = 'testindex';
    private $indexType = 'node';

    public function __construct(ContainerInterface $container, $sherlock)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getEntityManager();
        $this->sherlock = $sherlock;
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
                    ->index($this->indexName)
                    ->type($this->indexType)
                    ->document($doc);
                $doc->execute();
            }
        }
    }
}