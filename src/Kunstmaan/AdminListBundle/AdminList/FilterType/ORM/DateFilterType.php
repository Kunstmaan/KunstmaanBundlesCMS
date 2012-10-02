<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;

use DateTime;

use Symfony\Component\HttpFoundation\Request;

/**
 * DateFilterType
 */
class DateFilterType extends AbstractORMFilterType
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
            /* @todo get rid of hardcoded date formats below! */
            $date = DateTime::createFromFormat('d/m/Y', $data['value'])->format('Y-m-d');
            switch ($data['comparator']) {
                case 'before':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->lte($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    break;
                case 'after':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->gt($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    break;
            }
            $this->queryBuilder->setParameter('var_' . $uniqueId, $date);
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'KunstmaanAdminListBundle:FilterType:dateFilter.html.twig';
    }
}
