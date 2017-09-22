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
            '{{ bundle }}:{{ section }}',
{% endfor %}
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTemplates()
    {
        return [
            '{{ bundle }}:{{ template }}',
        ];
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