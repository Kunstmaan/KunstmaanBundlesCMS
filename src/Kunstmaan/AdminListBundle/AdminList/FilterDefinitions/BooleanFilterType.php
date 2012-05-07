<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterDefinitions;

class BooleanFilterType
{

    protected $columnname = null;
    protected $alias= null;
    
    protected $value = null;

    function __construct($columnname, $alias = "b")
    {
        $this->columnname = $columnname;
        $this->alias = $alias;
    }

    function bindRequest($request, &$data, $uniqueid)
    {
        $data['value'] = $request->query->get("filter_value_" . $uniqueid);
    }

    function adaptQueryBuilder($querybuilder, &$expressions, $data, $uniqueid)
    {
        if (isset($data['value'])) {
            switch ($data['value']) {
            case "true":
                $expressions[] = $querybuilder->expr()->eq($this->alias . '.' . $this->columnname, "true");
                break;
            case "false":
                $expressions[] = $querybuilder->expr()->like($this->alias . '.' . $this->columnname, "false");
                break;
            }
        }
    }

    function getTemplate()
    {
        return "KunstmaanAdminListBundle:Filters:booleanfilter.html.twig";
    }
}
