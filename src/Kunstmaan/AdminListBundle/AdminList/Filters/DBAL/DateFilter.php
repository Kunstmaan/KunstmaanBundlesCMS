<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters\DBAL;

use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\AdminListBundle\AdminList\Filters\AbstractFilter;
use Kunstmaan\AdminListBundle\AdminList\Provider\DoctrineDBALProvider;
use Kunstmaan\AdminListBundle\AdminList\Provider\ProviderInterface;

/**
 * DateFilter
 */
class DateFilter extends AbstractFilter
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
            throw new \InvalidArgumentException('You have to provide a DoctrineDBALProvider to apply the DBAL DateFilter!');
        }
        /* @var DoctrineDBALProvider $provider */
        $qb = $provider->getQueryBuilder();
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
