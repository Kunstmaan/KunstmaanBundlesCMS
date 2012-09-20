<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters;

use Doctrine\ORM\QueryBuilder;

use Symfony\Component\HttpFoundation\Request;

/**
 * NumberFilter
 */
class NumberFilter extends AbstractFilter
{
    /**
     * @param Request $request  The request
     * @param array   &$data    The data
     * @param string  $uniqueId The unique identifier
     */
    public function bindRequest(Request $request, &$data, $uniqueId)
    {
        $data['comparator'] = $request->query->get("filter_comparator_" . $uniqueId);
        $data['value']      = $request->query->get("filter_value_" . $uniqueId);
        $value2             = $request->query->get("filter_value2_" . $uniqueId);
        if (isset($value2)) {
            $data['value2'] = $value2;
        }
    }

    /**
     * @param QueryBuilder $queryBuilder The query builder
     * @param array        &$expressions The expressions
     * @param array        $data         The data
     * @param string       $uniqueId     The unique identifier
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder, &$expressions, $data, $uniqueId)
    {
        if (isset($data['value']) && isset($data['comparator'])) {
            switch ($data['comparator']) {
                case "eq":
                    $expressions[] = $queryBuilder->expr()->eq(
                        $this->alias . '.' . $this->columnName,
                        ":var_" . $uniqueId
                    );
                    break;
                case "neq":
                    $expressions[] = $queryBuilder->expr()->neq(
                        $this->alias . '.' . $this->columnName,
                        ":var_" . $uniqueId
                    );
                    break;
                case "lt":
                    $expressions[] = $queryBuilder->expr()->lt(
                        $this->alias . '.' . $this->columnName,
                        ":var_" . $uniqueId
                    );
                    break;
                case "lte":
                    $expressions[] = $queryBuilder->expr()->lte(
                        $this->alias . '.' . $this->columnName,
                        ":var_" . $uniqueId
                    );
                    break;
                case "gt":
                    $expressions[] = $queryBuilder->expr()->gt(
                        $this->alias . '.' . $this->columnName,
                        ":var_" . $uniqueId
                    );
                    break;
                case "gte":
                    $expressions[] = $queryBuilder->expr()->gte(
                        $this->alias . '.' . $this->columnName,
                        ":var_" . $uniqueId
                    );
                    break;
                case "isnull":
                    $expressions[] = $queryBuilder->expr()->isNull($this->alias . '.' . $this->columnName);

                    return;
                case "isnotnull":
                    $expressions[] = $queryBuilder->expr()->isNotNull($this->alias . '.' . $this->columnName);

                    return;
            }

            $queryBuilder->setParameter("var_" . $uniqueId, $data['value']);
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return "KunstmaanAdminListBundle:Filters:numberFilter.html.twig";
    }
}
