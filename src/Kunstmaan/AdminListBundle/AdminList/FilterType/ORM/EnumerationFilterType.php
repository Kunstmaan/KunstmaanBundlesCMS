<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;

use Symfony\Component\HttpFoundation\Request;

/**
 * EnumerationFilterType
 */
class EnumerationFilterType extends AbstractORMFilterType
{
    /**
     * @param Request $request  The request
     * @param array   &$data    The data
     * @param string  $uniqueId The unique identifier
     */
    public function bindRequest(Request $request, array &$data, $uniqueId)
    {
        $data['comparator'] = $request->query->get('filter_comparator_' . $uniqueId);
        $data['value'] = $request->query->get('filter_value_' . $uniqueId);
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
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->in($this->getAlias() . $this->columnName, ':var_' . $uniqueId));
                    $this->queryBuilder->setParameter('var_' . $uniqueId, $data['value'], \Doctrine\DBAL\Connection::PARAM_STR_ARRAY);

                    break;
                case 'notin':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->notIn($this->getAlias() . $this->columnName, ':var_' . $uniqueId));
                    $this->queryBuilder->setParameter('var_' . $uniqueId, $data['value'], \Doctrine\DBAL\Connection::PARAM_STR_ARRAY);

                    break;
            }
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'KunstmaanAdminListBundle:FilterType:enumerationFilter.html.twig';
    }
}
