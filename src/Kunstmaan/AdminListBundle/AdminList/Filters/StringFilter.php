<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters;

use Symfony\Component\HttpFoundation\Request;

class StringFilter extends AbstractFilter
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param array                                     $data
     * @param string                                    $uniqueId
     */
    public function bindRequest(Request $request, &$data, $uniqueId)
    {
        $data['comparator'] = $request->query->get("filter_comparator_" . $uniqueId);
        $data['value']      = $request->query->get("filter_value_" . $uniqueId);
    }

    /**
     * @param        $queryBuilder
     * @param array  $expressions
     * @param array  $data
     * @param string $uniqueId
     */
    public function adaptQueryBuilder($queryBuilder, &$expressions, $data, $uniqueId)
    {
        if (isset($data['value']) && isset($data['comparator'])) {
            switch ($data['comparator']) {
                case "equals":
                    $expressions[] = $queryBuilder->expr()->eq(
                        $this->alias . '.' . $this->columnName,
                        ":var_" . $uniqueId
                    );
                    $queryBuilder->setParameter('var_' . $uniqueId, $data['value']);
                    break;
                case "notequals":
                    $expressions[] = $queryBuilder->expr()->neq(
                        $this->alias . '.' . $this->columnName,
                        ":var_" . $uniqueId
                    );
                    $queryBuilder->setParameter('var_' . $uniqueId, $data['value']);
                    break;
                case "contains":
                    $expressions[] = $queryBuilder->expr()->like(
                        $this->alias . '.' . $this->columnName,
                        ":var_" . $uniqueId
                    );
                    $queryBuilder->setParameter('var_' . $uniqueId, '%' . $data['value'] . '%');
                    break;
                case "doesnotcontain":
                    $expressions[] = $queryBuilder->expr()->not(
                        $queryBuilder->expr()->like($this->alias . '.' . $this->columnName, ":var_" . $uniqueId)
                    );
                    $queryBuilder->setParameter('var_' . $uniqueId, '%' . $data['value'] . '%');
                    break;
                case "startswith":
                    $expressions[] = $queryBuilder->expr()->like(
                        $this->alias . '.' . $this->columnName,
                        ":var_" . $uniqueId
                    );
                    $queryBuilder->setParameter('var_' . $uniqueId, $data['value'] . '%');
                    break;
                case "endswith":
                    $expressions[] = $queryBuilder->expr()->like(
                        $this->alias . '.' . $this->columnName,
                        ":var_" . $uniqueId
                    );
                    $queryBuilder->setParameter('var_' . $uniqueId, '%' . $data['value']);
                    break;
            }
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return "KunstmaanAdminListBundle:Filters:stringFilter.html.twig";
    }
}
