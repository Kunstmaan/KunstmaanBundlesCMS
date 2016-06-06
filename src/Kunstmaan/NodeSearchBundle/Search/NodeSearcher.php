<?php

namespace Kunstmaan\NodeSearchBundle\Search;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\NodeSearchBundle\Helper\SearchBoostInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Default node searcher implementation
 *
 * @package Kunstmaan\NodeSearchBundle\Search
 */
class NodeSearcher extends AbstractElasticaSearcher
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage = null;

    /**
     * @var DomainConfigurationInterface
     */
    private $domainConfiguration;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var bool
     */
    private $useMatchQueryForTitle = false;

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function setTokenStorage(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param DomainConfigurationInterface $domainConfiguration
     */
    public function setDomainConfiguration(DomainConfigurationInterface $domainConfiguration)
    {
        $this->domainConfiguration = $domainConfiguration;
    }

    /**
     *
     * @param EntityManager $em
     */
     public function setEntityManager(EntityManager $em)
     {
         $this->em = $em;
     }

    /**
     * @param bool $useMatchQueryForTitle
     */
    public function setUseMatchQueryForTitle($useMatchQueryForTitle)
    {
        $this->useMatchQueryForTitle = $useMatchQueryForTitle;
    }

    /**
     * @param mixed  $query
     * @param string $type
     *
     * @return mixed|void
     */
    public function defineSearch($query, $type)
    {
        $query = \Elastica\Util::escapeTerm($query);

        $elasticaQueryString = new \Elastica\Query\Match();
        $elasticaQueryString
            ->setFieldMinimumShouldMatch('content', '80%')
            ->setFieldQuery('content', $query);

        if ($this->useMatchQueryForTitle) {
            $elasticaQueryTitle = new \Elastica\Query\Match();
            $elasticaQueryTitle
              ->setFieldQuery('title', $query)
              ->setFieldMinimumShouldMatch('title', '80%');
        } else {
            $elasticaQueryTitle = new \Elastica\Query\QueryString();
            $elasticaQueryTitle
              ->setDefaultField('title')
              ->setQuery($query);
        }

        $elasticaQueryBool = new \Elastica\Query\BoolQuery();
        $elasticaQueryBool
            ->addShould($elasticaQueryTitle)
            ->addShould($elasticaQueryString)
            ->setMinimumNumberShouldMatch(1);

        $this->applySecurityFilter($elasticaQueryBool);

        if (!is_null($type)) {
            $elasticaQueryType = new \Elastica\Query\Term();
            $elasticaQueryType->setTerm('type', $type);
            $elasticaQueryBool->addMust($elasticaQueryType);
        }

        $rootNode = $this->domainConfiguration->getRootNode();
        if (!is_null($rootNode)) {
            $elasticaQueryRoot = new \Elastica\Query\Term();
            $elasticaQueryRoot->setTerm('root_id', $rootNode->getId());
            $elasticaQueryBool->addMust($elasticaQueryRoot);
        }

        $rescore = new \Elastica\Rescore\Query();
        $rescore->setRescoreQuery($this->getPageBoosts());

        $this->query->setQuery($elasticaQueryBool);
        $this->query->setRescore($rescore);
        $this->query->setHighlight(
            array(
                'pre_tags'  => array('<strong>'),
                'post_tags' => array('</strong>'),
                'fields'    => array(
                    'content' => array(
                        'fragment_size'       => 150,
                        'number_of_fragments' => 3
                    )
                )
            )
        );
    }

    /**
     * Filter search results so only documents that are viewable by the current
     * user will be returned...
     *
     * @param \Elastica\Query\BoolQuery $elasticaQueryBool
     */
    protected function applySecurityFilter($elasticaQueryBool)
    {
        $roles = $this->getCurrentUserRoles();

        $elasticaQueryRoles = new \Elastica\Query\Terms();
        $elasticaQueryRoles
            ->setTerms('view_roles', $roles)
            ->setMinimumMatch(1);
        $elasticaQueryBool->addMust($elasticaQueryRoles);
    }

    /**
     * @return array
     */
    protected function getCurrentUserRoles()
    {
        $roles = array();
        if (!is_null($this->tokenStorage)) {
            $user = $this->tokenStorage->getToken()->getUser();
            if ($user instanceof BaseUser) {
                $roles = $user->getRoles();
            }
        }

        // Anonymous access should always be available for both anonymous & logged in users
        if (!in_array('IS_AUTHENTICATED_ANONYMOUSLY', $roles)) {
            $roles[] = 'IS_AUTHENTICATED_ANONYMOUSLY';
        }

        return $roles;
    }

    /**
     * Apply PageType specific and Page specific boosts
     *
     * @return \Elastica\Query\BoolQuery
     */
    protected function getPageBoosts()
    {
        $rescoreQueryBool = new \Elastica\Query\BoolQuery();

        //Apply page type boosts
        $pageClasses = $this->em->getRepository('KunstmaanNodeBundle:Node')->findAllDistinctPageClasses();
        foreach($pageClasses as $pageClass) {
            $page = new $pageClass['refEntityName']();

            if($page instanceof SearchBoostInterface) {
                $elasticaQueryTypeBoost = new \Elastica\Query\QueryString();
                $elasticaQueryTypeBoost
                    ->setBoost($page->getSearchBoost())
                    ->setDefaultField('page_class')
                    ->setQuery(addslashes($pageClass['refEntityName']));

                $rescoreQueryBool->addShould($elasticaQueryTypeBoost);
            }
        }

        //Apply page specific boosts
        $nodeSearches = $this->em->getRepository('KunstmaanNodeSearchBundle:NodeSearch')->findAll();
        foreach ($nodeSearches as $nodeSearch) {
            $elasticaQueryNodeId = new \Elastica\Query\QueryString();
            $elasticaQueryNodeId
                ->setBoost($nodeSearch->getBoost())
                ->setDefaultField('node_id')
                ->setQuery($nodeSearch->getNode()->getId());

            $rescoreQueryBool->addShould($elasticaQueryNodeId);
        }

        return $rescoreQueryBool;
    }
}
