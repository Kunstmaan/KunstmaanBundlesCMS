<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterDefinitions;

class NumberFilterType
{

    protected $columnname = null;
    protected $alias = null;
    
    function __construct($columnname, $alias = "b")
    {
        $this->columnname = $columnname;
        $this->alias = $alias;
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
        if (isset($data['value']) && isset($data['comparator'])) {
            switch ($data['comparator']) {
            case "eq":
                $expressions[] = $querybuilder->expr()->eq($this->prefix . '.' . $this->columnname, "?" . $uniqueid);
                break;
            case "neq":
                $expressions[] = $querybuilder->expr()->neq($this->prefix . '.' . $this->columnname, "?" . $uniqueid);
                break;
            case "lt":
                $expressions[] = $querybuilder->expr()->lt($this->prefix . '.' . $this->columnname, "?" . $uniqueid);
                break;
            case "lte":
                $expressions[] = $querybuilder->expr()->lte($this->prefix . '.' . $this->columnname, "?" . $uniqueid);
                break;
            case "gt":
                $expressions[] = $querybuilder->expr()->gt($this->prefix . '.' . $this->columnname, "?" . $uniqueid);
                break;
            case "gte":
                $expressions[] = $querybuilder->expr()->gte($this->prefix . '.' . $this->columnname, "?" . $uniqueid);
                break;
            case "isnull":
                $expressions[] = $querybuilder->expr()->isNull($this->prefix . '.' . $this->columnname);
                return;
            case "isnotnull":
                $expressions[] = $querybuilder->expr()->isNotNull($this->prefix . '.' . $this->columnname);
                return;
            }

            $querybuilder->setParameter($uniqueid, $data['value']);
        }
    }

    function getTemplate()
    {
        return "KunstmaanAdminListBundle:Filters:numberfilter.html.twig";
    }
}
