<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters\DBAL;

use Symfony\Component\HttpFoundation\Request;

/**
 * StringFilter
 */
class StringFilter extends AbstractDBALFilter
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
    }

    /**
     * @param array  $data     The data
     * @param string $uniqueId The unique identifier
     */
    public function apply($data, $uniqueId)
    {
        $qb = $this->getQueryBuilder();
        if (isset($data['value']) && isset($data['comparator'])) {
            switch ($data['comparator']) {
                case 'equals':
                    $qb->andWhere($qb->expr()->eq($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    $qb->setParameter('var_' . $uniqueId, $data['value']);
                    break;
                case 'notequals':
                    $qb->andWhere($qb->expr()->neq($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    $qb->setParameter('var_' . $uniqueId, $data['value']);
                    break;
                case 'contains':
                    $qb->andWhere($qb->expr()->like($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    $qb->setParameter('var_' . $uniqueId, '%' . $data['value'] . '%');
                    break;
                case 'doesnotcontain':
                    $qb->andWhere($this->alias . '.' . $this->columnName . ' NOT LIKE :var_' . $uniqueId);
                    $qb->setParameter('var_' . $uniqueId, '%' . $data['value'] . '%');
                    break;
                case 'startswith':
                    $qb->andWhere($qb->expr()->like($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    $qb->setParameter('var_' . $uniqueId, $data['value'] . '%');
                    break;
                case 'endswith':
                    $qb->andWhere($qb->expr()->like($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    $qb->setParameter('var_' . $uniqueId, '%' . $data['value']);
                    break;
            }
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'KunstmaanAdminListBundle:Filters:stringFilter.html.twig';
    }
}
