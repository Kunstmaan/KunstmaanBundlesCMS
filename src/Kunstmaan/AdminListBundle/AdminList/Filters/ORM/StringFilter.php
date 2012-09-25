<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters\ORM;

use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\AdminListBundle\AdminList\Filters\AbstractFilter;
use Kunstmaan\AdminListBundle\AdminList\Provider\DoctrineORMProvider;
use Kunstmaan\AdminListBundle\AdminList\Provider\ProviderInterface;

/**
 * StringFilter
 */
class StringFilter extends AbstractFilter
{
    /**
     * @param Request $request  The request
     * @param array   &$data    The data
     * @param string  $uniqueId The unique identifier
     */
    public function bindRequest(Request $request, &$data, $uniqueId)
    {
        $data['comparator'] = $request->query->get('filter_comparator_' . $uniqueId);
        $data['value']      = $request->query->get('filter_value_' . $uniqueId);
    }

    /**
     * @param ProviderInterface $provider     The provider
     * @param array             $data         The data
     * @param string            $uniqueId     The unique identifier
     */
    public function apply(ProviderInterface $provider, $data, $uniqueId)
    {
        if (!$provider instanceof DoctrineORMProvider) {
            throw new \InvalidArgumentException('You have to provide a DoctrineORMProvider to apply the ORM BooleanFilter!');
        }
        /* @var DoctrineORMProvider $provider */
        $qb = $provider->getQueryBuilder();
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
