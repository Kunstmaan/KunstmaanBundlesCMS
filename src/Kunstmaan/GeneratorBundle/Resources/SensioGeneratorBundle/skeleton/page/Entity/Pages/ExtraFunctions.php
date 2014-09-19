    /**
     * Returns the default backend form type for this page
     *
     * @return {{ adminType }}
     */
    public function getDefaultAdminType()
    {
        return new {{ adminType }}();
    }

    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return array();
    }

    /**
     * @return string[]
     */
    public function getPagePartAdminConfigurations()
    {
        return array(
{% for section in sections %}
            '{{ bundle }}:{{ section }}',
{% endfor %}
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTemplates()
    {
        return array('{{ bundle }}:{{ template }}');
    }

    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
        return '{{ bundle }}:Pages:Common/view.html.twig';
    }