<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters\DBAL;

use Symfony\Component\HttpFoundation\Request;

/**
 * BooleanFilter
 */
class BooleanFilter extends AbstractDBALFilter
{
    /**
     * @param Request $request The request
     * @param array   &$data    The data
     * @param string $uniqueId The unique identifier
     */
    public function bindRequest(Request $request, &$data, $uniqueId)
    {
        $data['value'] = $request->query->get('filter_value_' . $uniqueId);
    }

    /**
     * @param array  $data     The data
     * @param string $uniqueId The unique identifier
     */
    public function apply($data, $uniqueId)
    {
        $qb = $this->getQueryBuilder();
        if (isset($data['value'])) {
            switch ($data['value']) {
                case 'true':
                    $qb->andWhere($qb->expr()->eq($this->alias . '.' . $this->columnName, 'true'));
                    break;
                case 'false':
                    $qb->andWhere($qb->expr()->eq($this->alias . '.' . $this->columnName, 'false'));
                    break;
            }
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'KunstmaanAdminListBundle:Filters:booleanFilter.html.twig';
    }
}
