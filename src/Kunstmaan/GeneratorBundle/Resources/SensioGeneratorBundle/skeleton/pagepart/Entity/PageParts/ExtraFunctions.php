    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
        return '{% if not isV4 %}{{ bundle }}:{%endif%}PageParts/{{ pagepart }}{% if not isV4 %}:{% else %}/{% endif %}view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return {{ adminType }}::class;
    }
