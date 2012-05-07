<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterDefinitions;

class BooleanFilterType
{

    protected $columnname = null;

    protected $value = null;

    function __construct($columnname)
    {
        $this->columnname = $columnname;
    }

    function bindRequest($request, &$data, $uniqueid)
    {
        $data['value'] = $request->query->get("filter_value_" . $uniqueid);
    }

    function adaptQueryBuilder($querybuilder, &$expressions, $data, $uniqueid)
    {
        $prefix = '';
        if (!strpos($this->columnname, '.')) {
            $prefix = 'b.';
        }
        if (isset($data['value'])) {
            switch ($data['value']) {
            case "true":
                $expressions[] = $querybuilder->expr()->eq($prefix . $this->columnname, "true");
                break;
            case "false":
                $expressions[] = $querybuilder->expr()->like($prefix . $this->columnname, "false");
                break;
            }
        }
    }

    function getTemplate()
    {
        return "KunstmaanAdminListBundle:Filters:booleanfilter.html.twig";
    }
}
