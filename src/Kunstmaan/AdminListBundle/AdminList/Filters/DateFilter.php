<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters;

use Doctrine\ORM\QueryBuilder;

use Symfony\Component\HttpFoundation\Request;

/**
 * DateFilter
 */
class DateFilter extends AbstractFilter
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
            $date = \DateTime::createFromFormat('d/m/Y', $data['value'])->format('Y-m-d');
            switch ($data['comparator']) {
                case "before":
                    $expressions[] = $queryBuilder->expr()->lte(
                        $this->alias . '.' . $this->columnName,
                        ":var_" . $uniqueId
                    );
                    $queryBuilder->setParameter('var_' . $uniqueId, $date);
                    break;
                case "after":
                    $expressions[] = $queryBuilder->expr()->gt(
                        $this->alias . '.' . $this->columnName,
                        ":var_" . $uniqueId
                    );
                    $queryBuilder->setParameter('var_' . $uniqueId, $date);
                    break;
            }
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return "KunstmaanAdminListBundle:Filters:dateFilter.html.twig";
    }
}
