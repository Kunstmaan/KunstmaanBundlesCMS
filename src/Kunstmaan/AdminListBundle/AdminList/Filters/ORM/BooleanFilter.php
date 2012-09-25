<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters\ORM;

use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\AdminListBundle\AdminList\Filters\AbstractFilter;
use Kunstmaan\AdminListBundle\AdminList\Provider\DoctrineORMProvider;
use Kunstmaan\AdminListBundle\AdminList\Provider\ProviderInterface;

/**
 * BooleanFilter
 */
class BooleanFilter extends AbstractFilter
{
    /**
     * @param Request $request  The request
     * @param array   &$data    The data
     * @param string  $uniqueId The unique identifier
     */
    public function bindRequest(Request $request, &$data, $uniqueId)
    {
        $data['value'] = $request->query->get('filter_value_' . $uniqueId);
    }

    /**
     * @param ProviderInterface $provider The provider
     * @param array             $data     The data
     * @param string            $uniqueId The unique identifier
     */
    public function apply(ProviderInterface $provider, $data, $uniqueId)
    {
        if (!$provider instanceof DoctrineORMProvider) {
            throw new \InvalidArgumentException('You have to provide a DoctrineORMProvider to apply the ORM BooleanFilter!');
        }
        /* @var DoctrineORMProvider $provider */
        $qb = $provider->getQueryBuilder();
        if (isset($data['value'])) {
            switch ($data['value']) {
                case 'true':
                    $qb->andWhere($qb->expr()->eq($this->alias . '.' . $this->columnName, 'true'));
                    break;
                case 'false':
                    $qb->andWhere($qb->expr()->eq($this->alias . '.' . $this->columnName, 'false'));
                    break;
            }
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'KunstmaanAdminListBundle:Filters:booleanFilter.html.twig';
    }
}
