<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;

use Symfony\Component\HttpFoundation\InputBag;
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
        $valueId = 'filter_value_' . $uniqueId;
        $data['value'] = class_exists(InputBag::class) ? $request->query->all($valueId) : $request->query->get($valueId);
    }

    /**
     * @param array  $data     The data
     * @param string $uniqueId The unique identifier
     */
    public function apply(array $data, $uniqueId)
    {
        if (isset($data['value'], $data['comparator'])) {
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
        return '@KunstmaanAdminList/FilterType/enumerationFilter.html.twig';
    }
}
