<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters;

use Doctrine\ORM\QueryBuilder;

use Symfony\Component\HttpFoundation\Request;

/**
 * BooleanFilter
 */
class BooleanFilter extends AbstractFilter
{
    /**
     * @param Request $request  The request
     * @param array   &$data    The data
     * @param string  $uniqueId The unique identifier
     */
    public function bindRequest(Request $request, &$data, $uniqueId)
    {
        $data['value'] = $request->query->get("filter_value_" . $uniqueId);
    }

    /**
     * @param QueryBuilder $queryBuilder The query builder
     * @param array        &$expressions The expressions
     * @param array        $data         The data
     * @param string       $uniqueId     The unique identifier
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder, &$expressions, $data, $uniqueId)
    {
        if (isset($data['value'])) {
            switch ($data['value']) {
                case "true":
                    $expressions[] = $queryBuilder->expr()->eq($this->alias . '.' . $this->columnName, "true");
                    break;
                case "false":
                    $expressions[] = $queryBuilder->expr()->like($this->alias . '.' . $this->columnName, "false");
                    break;
            }
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return "KunstmaanAdminListBundle:Filters:booleanFilter.html.twig";
    }
}
