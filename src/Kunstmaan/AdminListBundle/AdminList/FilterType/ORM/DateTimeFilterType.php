<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;

use DateTime;

use Symfony\Component\HttpFoundation\Request;

/**
 * DateTimeFilterType
 */
class DateTimeFilterType extends AbstractORMFilterType
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
    public function apply(array $data, $uniqueId)
    {
        if (isset($data['value']) && isset($data['comparator'])) {
            /** @var DateTime $datetime */
            $date = empty($data['value']['date']) ? date('d/m/Y') : $data['value']['date'];
            $time = empty($data['value']['time']) ? date('H:i')   : $data['value']['time'];
            $datetime = DateTime::createFromFormat('d/m/Y H:i', $date . ' ' . $time)->format('Y-m-d H:i');

            switch ($data['comparator']) {
                case 'before':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->lte($this->getAlias() . $this->columnName, ':var_' . $uniqueId));
                    break;
                case 'after':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->gt($this->getAlias() . $this->columnName, ':var_' . $uniqueId));
                    break;
            }
            $this->queryBuilder->setParameter('var_' . $uniqueId, $datetime);
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'KunstmaanAdminListBundle:FilterType:dateTimeFilter.html.twig';
    }
}
