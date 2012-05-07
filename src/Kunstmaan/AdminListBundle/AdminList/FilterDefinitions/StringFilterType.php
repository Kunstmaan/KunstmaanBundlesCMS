<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterDefinitions;

class StringFilterType
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
    }

    function adaptQueryBuilder($querybuilder, &$expressions, $data, $uniqueid)
    {
        if (isset($data['value']) && isset($data['comparator'])) {
            $prefix = '';
            if (!strpos($this->columnname, '.')) {
                $prefix = 'b.';
            }
            switch ($data['comparator']) {
            case "equals":
                $expressions[] = $querybuilder->expr()->eq($prefix . $this->columnname, "?" . $uniqueid);
                $querybuilder->setParameter($uniqueid, $data['value']);
                break;
            case "notequals":
                $expressions[] = $querybuilder->expr()->neq($prefix . $this->columnname, "?" . $uniqueid);
                $querybuilder->setParameter($uniqueid, $data['value']);
                break;
            case "contains":
                $expressions[] = $querybuilder->expr()->like($prefix . $this->columnname, "?" . $uniqueid);
                $querybuilder->setParameter($uniqueid, '%' . $data['value'] . '%');
                break;
            case "doesntcontain":
                $expressions[] = $querybuilder->expr()->not($querybuilder->expr()->like($prefix . $this->columnname, "?" . $uniqueid));
                $querybuilder->setParameter($uniqueid, '%' . $data['value'] . '%');
                break;
            case "startswith":
                $expressions[] = $querybuilder->expr()->like($prefix . $this->columnname, "?" . $uniqueid);
                $querybuilder->setParameter($uniqueid, $data['value'] . '%');
                break;
            case "endswith":
                $expressions[] = $querybuilder->expr()->like($prefix . $this->columnname, "?" . $uniqueid);
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
