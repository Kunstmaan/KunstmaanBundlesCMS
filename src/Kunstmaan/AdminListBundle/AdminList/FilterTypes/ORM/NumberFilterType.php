<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters\ORM;

use Symfony\Component\HttpFoundation\Request;

/**
 * NumberFilterType
 */
class NumberFilterType extends AbstractORMFilterType
{
    /**
     * @param Request $request  The request
     * @param array   &$data    The data
     * @param string  $uniqueId The unique identifier
     */
    public function bindRequest(Request $request, array &$data, $uniqueId)
    {
        $data['comparator'] = $request->query->get('filter_comparator_' . $uniqueId);
        $data['value']      = $request->query->get('filter_value_' . $uniqueId);
    }

    /**
     * @param array  $data     The data
     * @param string $uniqueId The unique identifier
     */
    public function apply($data, $uniqueId)
    {
        if (isset($data['value']) && isset($data['comparator'])) {
            switch ($data['comparator']) {
                case 'eq':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->eq($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    break;
                case 'neq':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->neq($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    break;
                case 'lt':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->lt($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    break;
                case 'lte':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->lte($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    break;
                case 'gt':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->gt($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    break;
                case 'gte':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->gte($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    break;
                case 'isnull':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->isNull($this->alias . '.' . $this->columnName));

                    return;
                case 'isnotnull':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->isNotNull($this->alias . '.' . $this->columnName));

                    return;
            }
            $this->queryBuilder->setParameter('var_' . $uniqueId, $data['value']);
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'KunstmaanAdminListBundle:FilterTypes:numberFilter.html.twig';
    }
}
