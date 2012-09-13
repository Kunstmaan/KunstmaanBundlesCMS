<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters;

use Symfony\Component\HttpFoundation\Request;

class BooleanFilter extends AbstractFilter
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param array                                     $data
     * @param string                                    $uniqueId
     */
    public function bindRequest(Request $request, &$data, $uniqueId)
    {
        $data['value'] = $request->query->get("filter_value_" . $uniqueId);
    }

    /**
     * @param        $queryBuilder
     * @param array  $expressions
     * @param array  $data
     * @param string $uniqueId
     */
    public function adaptQueryBuilder($queryBuilder, &$expressions, $data, $uniqueId)
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
