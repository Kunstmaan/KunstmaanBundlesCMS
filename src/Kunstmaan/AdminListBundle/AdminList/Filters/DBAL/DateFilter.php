<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters\DBAL;

use Symfony\Component\HttpFoundation\Request;

/**
 * DateFilter
 */
class DateFilter extends AbstractDBALFilter
{
    /**
     * @param Request $request The request
     * @param array   &$data    The data
     * @param string $uniqueId The unique identifier
     */
    public function bindRequest(Request $request, &$data, $uniqueId)
    {
        $data['comparator'] = $request->query->get('filter_comparator_' . $uniqueId);
        $data['value']      = $request->query->get('filter_value_' . $uniqueId);
        $value2             = $request->query->get('filter_value2_' . $uniqueId);
        if (isset($value2)) {
            $data['value2'] = $value2;
        }
    }

    /**
     * @param array  $data     The data
     * @param string $uniqueId The unique identifier
     */
    public function apply($data, $uniqueId)
    {
        $qb = $this->getQueryBuilder();
        if (isset($data['value']) && isset($data['comparator'])) {
            /* @todo get rid of hardcoded date formats below! */
            $date = \DateTime::createFromFormat('d/m/Y', $data['value'])->format('Y-m-d');
            switch ($data['comparator']) {
                case 'before':
                    $qb->andWhere($qb->expr()->lte($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    break;
                case 'after':
                    $qb->andWhere($qb->expr()->gt($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    break;
            }
            $qb->setParameter('var_' . $uniqueId, $date);
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'KunstmaanAdminListBundle:Filters:dateFilter.html.twig';
    }
}
