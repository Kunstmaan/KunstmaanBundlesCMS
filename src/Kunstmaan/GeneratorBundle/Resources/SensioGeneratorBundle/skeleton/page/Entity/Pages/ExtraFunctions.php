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
            '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}{{ section }}',
{% endfor %}
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTemplates()
    {
        return ['{% if not isV4 %}{{ bundle.getName() }}:{%endif%}{{ template }}'];
    }

    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
        return '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}Pages/Common{% if not isV4 %}:{% else %}/{% endif %}view.html.twig';
    }