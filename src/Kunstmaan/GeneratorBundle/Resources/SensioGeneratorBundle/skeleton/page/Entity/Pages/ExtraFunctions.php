    /**
     * Returns the default backend form type for this page.
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return {{ adminType }}::class;
    }

    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function getPagePartAdminConfigurations()
    {
        return [
{% for section in sections %}
            '{% if not isV4 %}{{ bundle }}:{%endif%}{{ section }}',
{% endfor %}
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTemplates()
    {
        return ['{% if not isV4 %}{{ bundle }}:{%endif%}{{ template }}'];
    }

    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
        return '{% if not isV4 %}{{ bundle }}:{%endif%}Pages/Common{% if not isV4 %}:{% else %}/{% endif %}view.html.twig';
    }
