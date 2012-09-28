<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters\ORM;

use Symfony\Component\HttpFoundation\Request;

/**
 * BooleanFilterType
 */
class BooleanFilterType extends AbstractORMFilterType
{
    /**
     * @param Request $request  The request
     * @param array   &$data    The data
     * @param string  $uniqueId The unique identifier
     */
    public function bindRequest(Request $request, array &$data, $uniqueId)
    {
        $data['value'] = $request->query->get('filter_value_' . $uniqueId);
    }

    /**
     * @param array  $data     The data
     * @param string $uniqueId The unique identifier
     */
    public function apply($data, $uniqueId)
    {
        if (isset($data['value'])) {
            switch ($data['value']) {
                case 'true':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->eq($this->alias . '.' . $this->columnName, 'true'));
                    break;
                case 'false':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->eq($this->alias . '.' . $this->columnName, 'false'));
                    break;
            }
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'KunstmaanAdminListBundle:FilterTypes:booleanFilter.html.twig';
    }
}
