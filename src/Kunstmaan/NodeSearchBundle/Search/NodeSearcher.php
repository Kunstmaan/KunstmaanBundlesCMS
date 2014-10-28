<?php

namespace Kunstmaan\NodeSearchBundle\Search;

use Kunstmaan\AdminBundle\Entity\BaseUser;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class NodeSearcher
 *
 * Default node searcher implementation
 *
 * @package Kunstmaan\NodeSearchBundle\Search
 */
class NodeSearcher extends AbstractElasticaSearcher
{
    /** @var SecurityContextInterface */
    private $securityContext = null;

    public function setSecurityContext(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @param mixed  $query
     * @param string $lang
     * @param string $type
     *
     * @return mixed|void
     */
    public function defineSearch($query, $lang, $type)
    {
        $elasticaQueryLang = new \Elastica\Query\Term();
        $elasticaQueryLang->setTerm('lang', $lang);

        $elasticaQueryString = new \Elastica\Query\Match();
        $elasticaQueryString
            ->setFieldQuery('content', $query)
            ->setFieldMinimumShouldMatch('content', '80%');

        $elasticaQueryTitle = new \Elastica\Query\QueryString();
        $elasticaQueryTitle
            ->setDefaultField('title')
            ->setBoost(2.0)
            ->setQuery($query);

        $elasticaQueryBool = new \Elastica\Query\Bool();
        $elasticaQueryBool
            ->addMust($elasticaQueryLang)
            ->addShould($elasticaQueryTitle)
            ->addShould($elasticaQueryString)
            ->setMinimumNumberShouldMatch(1);

        $this->applySecurityContext($elasticaQueryBool);

        if (!is_null($type)) {
            $elasticaQueryType = new \Elastica\Query\Term();
            $elasticaQueryType->setTerm('type', $type);
            $elasticaQueryBool->addMust($elasticaQueryType);
        }

        $this->query->setQuery($elasticaQueryBool);
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
     * Filter search results so only documents that are viewable by the current user will be returned...
     *
     * @param $elasticaQueryBool
     */
    protected function applySecurityContext($elasticaQueryBool)
    {
        $roles = array();
        if (!is_null($this->securityContext)) {
            $user = $this->securityContext->getToken()->getUser();
            if ($user instanceof BaseUser) {
                $roles = $user->getRoles();
            }
        }

        // Anonymous access should always be available for both anonymous & logged in users
        if (!in_array('IS_AUTHENTICATED_ANONYMOUSLY', $roles)) {
            $roles[] = 'IS_AUTHENTICATED_ANONYMOUSLY';
        }

        $elasticaQueryRoles = new \Elastica\Query\Terms();
        $elasticaQueryRoles
            ->setTerms('view_roles', $roles)
            ->setMinimumMatch(1);
        $elasticaQueryBool->addMust($elasticaQueryRoles);
    }
}
