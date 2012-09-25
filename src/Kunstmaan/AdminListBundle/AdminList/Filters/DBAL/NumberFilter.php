<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters\DBAL;

use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\AdminListBundle\AdminList\Filters\AbstractFilter;
use Kunstmaan\AdminListBundle\AdminList\Provider\DoctrineDBALProvider;
use Kunstmaan\AdminListBundle\AdminList\Provider\ProviderInterface;

/**
 * NumberFilter
 */
class NumberFilter extends AbstractFilter
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
     * @param ProviderInterface $provider The provider
     * @param array             $data     The data
     * @param string            $uniqueId The unique identifier
     */
    public function apply(ProviderInterface $provider, $data, $uniqueId)
    {
        if (!$provider instanceof DoctrineDBALProvider) {
            throw new \InvalidArgumentException('You have to provide a DoctrineDBALProvider to apply the DBAL NumberFilter!');
        }
        $qb = $provider->getQueryBuilder();
        if (isset($data['value']) && isset($data['comparator'])) {
            switch ($data['comparator']) {
                case 'eq':
                    $qb->andWhere($qb->expr()->eq($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    break;
                case 'neq':
                    $qb->andWhere($qb->expr()->neq($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    break;
                case 'lt':
                    $qb->andWhere($qb->expr()->lt($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    break;
                case 'lte':
                    $qb->andWhere($qb->expr()->lte($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    break;
                case 'gt':
                    $qb->andWhere($qb->expr()->gt($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    break;
                case 'gte':
                    $qb->andWhere($qb->expr()->gte($this->alias . '.' . $this->columnName, ':var_' . $uniqueId));
                    break;
                case 'isnull':
                    $qb->andWhere($qb->expr()->isNull($this->alias . '.' . $this->columnName));

                    return;
                case 'isnotnull':
                    $qb->andWhere($qb->expr()->isNotNull($this->alias . '.' . $this->columnName));

                    return;
            }
            $qb->setParameter('var_' . $uniqueId, $data['value']);
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'KunstmaanAdminListBundle:Filters:numberFilter.html.twig';
    }
}
