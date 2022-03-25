<?php

namespace Kunstmaan\NodeSearchBundle\Search;

use Doctrine\ORM\EntityManager;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchQuery;
use Elastica\Query\QueryString;
use Elastica\Query\Term;
use Elastica\Util;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeSearchBundle\Entity\NodeSearch;
use Kunstmaan\NodeSearchBundle\Helper\SearchBoostInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

/**
 * Default node searcher implementation
 */
class NodeSearcher extends AbstractElasticaSearcher
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var DomainConfigurationInterface
     */
    protected $domainConfiguration;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var bool
     */
    protected $useMatchQueryForTitle = false;

    public function setTokenStorage(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function setDomainConfiguration(DomainConfigurationInterface $domainConfiguration)
    {
        $this->domainConfiguration = $domainConfiguration;
    }

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
     * @param string $type
     *
     * @return mixed|void
     */
    public function defineSearch($query, $type)
    {
        $query = Util::escapeTerm($query);

        $elasticaQueryString = new MatchQuery();
        $elasticaQueryString
            ->setFieldMinimumShouldMatch('content', '80%')
            ->setFieldQuery('content', $query);

        if ($this->useMatchQueryForTitle) {
            $elasticaQueryTitle = new MatchQuery();
            $elasticaQueryTitle
                ->setFieldQuery('title', $query)
                ->setFieldMinimumShouldMatch('title', '80%')
                ->setFieldBoost('title', 2)
            ;
        } else {
            $elasticaQueryTitle = new QueryString();
            $elasticaQueryTitle
              ->setDefaultField('title')
              ->setQuery($query);
        }

        $elasticaQueryBool = new BoolQuery();
        $elasticaQueryBool
            ->addShould($elasticaQueryTitle)
            ->addShould($elasticaQueryString)
            ->setMinimumShouldMatch(1);

        $this->applySecurityFilter($elasticaQueryBool);

        if (!\is_null($type)) {
            $elasticaQueryType = new Term();
            $elasticaQueryType->setTerm('type', $type);
            $elasticaQueryBool->addMust($elasticaQueryType);
        }

        $rootNode = $this->domainConfiguration->getRootNode();
        if (!\is_null($rootNode)) {
            $elasticaQueryRoot = new Term();
            $elasticaQueryRoot->setTerm('root_id', $rootNode->getId());
            $elasticaQueryBool->addMust($elasticaQueryRoot);
        }

        $rescore = new \Elastica\Rescore\Query();
        $rescore->setRescoreQuery($this->getPageBoosts());

        $this->query->setQuery($elasticaQueryBool);
        $this->query->setRescore($rescore);
        $this->query->setHighlight(
            [
                'pre_tags' => ['<strong>'],
                'post_tags' => ['</strong>'],
                'fields' => [
                    'content' => [
                        'fragment_size' => 150,
                        'number_of_fragments' => 3,
                    ],
                ],
            ]
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

        $elasticaQueryRoles = new Query\Terms('view_roles', $roles);
        $elasticaQueryBool->addMust($elasticaQueryRoles);
    }

    /**
     * @return array
     */
    protected function getCurrentUserRoles()
    {
        $roles = [];
        if (!\is_null($this->tokenStorage)) {
            $token = $this->tokenStorage->getToken();
            $user = $token instanceof TokenInterface ? $token->getUser() : null;
            if ($user instanceof BaseUser) {
                $roles = $user->getRoles();
            }
        }

        // Anonymous access should always be available for both anonymous & logged in users
        // NEXT_MAJOR cleanup old security role
        if (!\in_array('IS_AUTHENTICATED_ANONYMOUSLY', $roles, true)) {
            $roles[] = 'IS_AUTHENTICATED_ANONYMOUSLY';
        }

        if (defined(AuthenticatedVoter::PUBLIC_ACCESS) && !\in_array(AuthenticatedVoter::PUBLIC_ACCESS, $roles, true)) {
            $roles[] = AuthenticatedVoter::PUBLIC_ACCESS;
        }

        // Return a re-indexed array to make sure the array keys are incremental and don't skip a number. Otherwise
        // this causes issues in ES7.
        return array_values($roles);
    }

    /**
     * Apply PageType specific and Page specific boosts
     *
     * @return \Elastica\Query\BoolQuery
     */
    protected function getPageBoosts()
    {
        $rescoreQueryBool = new BoolQuery();

        // Apply page type boosts
        $pageClasses = $this->em->getRepository(Node::class)->findAllDistinctPageClasses();
        foreach ($pageClasses as $pageClass) {
            $page = new $pageClass['refEntityName']();

            if ($page instanceof SearchBoostInterface) {
                $elasticaQueryTypeBoost = new QueryString();
                $elasticaQueryTypeBoost
                    ->setBoost($page->getSearchBoost())
                    ->setDefaultField('page_class')
                    ->setQuery(addslashes($pageClass['refEntityName']));

                $rescoreQueryBool->addShould($elasticaQueryTypeBoost);
            }
        }

        // Apply page specific boosts
        $nodeSearches = $this->em->getRepository(NodeSearch::class)->findAll();
        foreach ($nodeSearches as $nodeSearch) {
            $elasticaQueryNodeId = new QueryString();
            $elasticaQueryNodeId
                ->setBoost($nodeSearch->getBoost())
                ->setDefaultField('node_id')
                ->setQuery($nodeSearch->getNode()->getId());

            $rescoreQueryBool->addShould($elasticaQueryNodeId);
        }

        return $rescoreQueryBool;
    }
}
