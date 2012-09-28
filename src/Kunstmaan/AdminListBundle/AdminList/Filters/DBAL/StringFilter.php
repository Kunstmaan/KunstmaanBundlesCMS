<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters\DBAL;

use Symfony\Component\HttpFoundation\Request;

/**
 * StringFilter
 */
class StringFilter extends AbstractDBALFilter
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
                case 'equals':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->eq($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    $this->queryBuilder->setParameter('var_' . $uniqueId, $data['value']);
                    break;
                case 'notequals':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->neq($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    $this->queryBuilder->setParameter('var_' . $uniqueId, $data['value']);
                    break;
                case 'contains':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->like($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    $this->queryBuilder->setParameter('var_' . $uniqueId, '%' . $data['value'] . '%');
                    break;
                case 'doesnotcontain':
                    $this->queryBuilder->andWhere($this->alias . '.' . $this->columnName . ' NOT LIKE :var_' . $uniqueId);
                    $this->queryBuilder->setParameter('var_' . $uniqueId, '%' . $data['value'] . '%');
                    break;
                case 'startswith':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->like($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    $this->queryBuilder->setParameter('var_' . $uniqueId, $data['value'] . '%');
                    break;
                case 'endswith':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->like($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    $this->queryBuilder->setParameter('var_' . $uniqueId, '%' . $data['value']);
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
