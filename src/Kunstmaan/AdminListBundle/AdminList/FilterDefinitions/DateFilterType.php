<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterDefinitions;

class DateFilterType
{

    protected $columnname = null;

    function __construct($columnname)
    {
        $this->columnname = $columnname;
    }

    function bindRequest($request, &$data, $uniqueid)
    {
        $data['comparator'] = $request->query->get("filter_comparator_" . $uniqueid);
        $data['value'] = $request->query->get("filter_value_" . $uniqueid);
        $value2 = $request->query->get("filter_value2_" . $uniqueid);
        if (isset($value2))
            $data['value2'] = $request->query->get("filter_value2_" . $uniqueid);
    }

    function adaptQueryBuilder($querybuilder, &$expressions, $data, $uniqueid)
    {
        $prefix = '';
        if (!strpos($this->columnname, '.')) {
            $prefix = 'b.';
        }
        if (isset($data['value']) && isset($data['comparator'])) {
            switch ($data['comparator']) {
            case "before":
                $expressions[] = $querybuilder->expr()->lte($prefix . $this->columnname, "?" . $uniqueid);
                $querybuilder->setParameter($uniqueid, $data['value']);
                break;
            case "after":
                $expressions[] = $querybuilder->expr()->gt($prefix . $this->columnname, "?" . $uniqueid);
                $querybuilder->setParameter($uniqueid, $data['value']);
                break;
            }
        }
    }

    function getTemplate()
    {
        return "KunstmaanAdminListBundle:Filters:datefilter.html.twig";
    }
}
