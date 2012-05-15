<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterDefinitions;

class StringFilterType
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
    }

    function adaptQueryBuilder($querybuilder, &$expressions, $data, $uniqueid)
    {
        if (isset($data['value']) && isset($data['comparator'])) {
            switch ($data['comparator']) {
            case "equals":
                $expressions[] = $querybuilder->expr()->eq($this->alias . '.' . $this->columnname, "?" . $uniqueid);
                $querybuilder->setParameter($uniqueid, $data['value']);
                break;
            case "notequals":
                $expressions[] = $querybuilder->expr()->neq($this->alias . '.' . $this->columnname, "?" . $uniqueid);
                $querybuilder->setParameter($uniqueid, $data['value']);
                break;
            case "contains":
                $expressions[] = $querybuilder->expr()->like($this->alias . '.' . $this->columnname, "?" . $uniqueid);
                $querybuilder->setParameter($uniqueid, '%' . $data['value'] . '%');
                break;
            case "doesntcontain":
                $expressions[] = $querybuilder->expr()->not($querybuilder->expr()->like($this->alias . '.' . $this->columnname, "?" . $uniqueid));
                $querybuilder->setParameter($uniqueid, '%' . $data['value'] . '%');
                break;
            case "startswith":
                $expressions[] = $querybuilder->expr()->like($this->alias . '.' . $this->columnname, "?" . $uniqueid);
                $querybuilder->setParameter($uniqueid, $data['value'] . '%');
                break;
            case "endswith":
                $expressions[] = $querybuilder->expr()->like($this->alias . '.' . $this->columnname, "?" . $uniqueid);
                $querybuilder->setParameter($uniqueid, '%' . $data['value']);
                break;
            }
        }
    }

    function getTemplate()
    {
        return "KunstmaanAdminListBundle:Filters:stringfilter.html.twig";
    }
}
