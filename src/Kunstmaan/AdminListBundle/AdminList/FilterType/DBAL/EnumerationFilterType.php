<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL;

use Symfony\Component\HttpFoundation\Request;

/**
 * EnumerationFilterType
 */
class EnumerationFilterType extends AbstractDBALFilterType
{
    private $comparator;

    private $value;

    /**
     * @param Request $request  The request
     * @param array   &$data    The data
     * @param string  $uniqueId The unique identifier
     */
    public function bindRequest(Request $request, array &$data, $uniqueId)
    {
        $this->comparator = $data['comparator'] = $request->query->get('filter_comparator_' . $uniqueId);
        $this->value = $data['value'] = $request->query->get('filter_value_' . $uniqueId);
    }

    /**
     * @param array  $data     The data
     * @param string $uniqueId The unique identifier
     */
    public function apply(array $data, $uniqueId)
    {
        if (isset($data['value']) && isset($data['comparator'])) {
            switch ($data['comparator']) {
                case 'in':
                    $this->queryBuilder->andWhere($this->getAlias() . $this->columnName . ' IN (:var_' . $uniqueId . ')');
                    $this->queryBuilder->setParameter('var_' . $uniqueId, $data['value'], \Doctrine\DBAL\Connection::PARAM_STR_ARRAY);

                    break;
                case 'notin':
                    $this->queryBuilder->andWhere($this->getAlias() . $this->columnName . ' NOT IN (:var_' . $uniqueId . ')');
                    $this->queryBuilder->setParameter('var_' . $uniqueId, $data['value'], \Doctrine\DBAL\Connection::PARAM_STR_ARRAY);

                    break;
            }
        }
    }

    public function getComparator()
    {
        return $this->comparator;
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'KunstmaanAdminListBundle:FilterType:enumerationFilter.html.twig';
    }
}
