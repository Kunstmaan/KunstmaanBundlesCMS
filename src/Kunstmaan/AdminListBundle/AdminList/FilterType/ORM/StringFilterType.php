<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;

use Symfony\Component\HttpFoundation\Request;

/**
 * StringFilterType
 */
class StringFilterType extends AbstractORMFilterType
{
    /**
     * @param array  &$data
     * @param string $uniqueId
     */
    public function bindRequest(Request $request, array &$data, $uniqueId)
    {
        $data['comparator'] = $request->query->get('filter_comparator_' . $uniqueId);
        $data['value'] = $request->query->get('filter_value_' . $uniqueId);
    }

    /**
     * @param string $uniqueId
     */
    public function apply(array $data, $uniqueId)
    {
        if (isset($data['value'], $data['comparator'])) {
            switch ($data['comparator']) {
                case 'equals':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->eq($this->getAlias() . $this->columnName, ':var_' . $uniqueId));
                    $this->queryBuilder->setParameter('var_' . $uniqueId, $data['value']);

                    break;
                case 'notequals':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->neq($this->getAlias() . $this->columnName, ':var_' . $uniqueId));
                    $this->queryBuilder->setParameter('var_' . $uniqueId, $data['value']);

                    break;
                case 'contains':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->like($this->getAlias() . $this->columnName, ':var_' . $uniqueId));
                    $this->queryBuilder->setParameter('var_' . $uniqueId, '%' . $data['value'] . '%');

                    break;
                case 'doesnotcontain':
                    $this->queryBuilder->andWhere($this->getAlias() . $this->columnName . ' NOT LIKE :var_' . $uniqueId);
                    $this->queryBuilder->setParameter('var_' . $uniqueId, '%' . $data['value'] . '%');

                    break;
                case 'startswith':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->like($this->getAlias() . $this->columnName, ':var_' . $uniqueId));
                    $this->queryBuilder->setParameter('var_' . $uniqueId, $data['value'] . '%');

                    break;
                case 'endswith':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->like($this->getAlias() . $this->columnName, ':var_' . $uniqueId));
                    $this->queryBuilder->setParameter('var_' . $uniqueId, '%' . $data['value']);

                    break;
                case 'empty':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->orX(
                        $this->queryBuilder->expr()->isNull($this->getAlias() . $this->columnName),
                        $this->queryBuilder->expr()->eq($this->getAlias() . $this->columnName, '\'-\''),
                        $this->queryBuilder->expr()->eq($this->getAlias() . $this->columnName, ':var_empty_' . $uniqueId)
                    ));
                    $this->queryBuilder->setParameter('var_empty_' . $uniqueId, '');

                    break;
            }
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return '@KunstmaanAdminList/FilterType/stringFilter.html.twig';
    }
}
