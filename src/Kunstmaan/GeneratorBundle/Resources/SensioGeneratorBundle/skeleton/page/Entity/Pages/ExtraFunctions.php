    public function getDefaultAdminType(): string
    {
        return {{ adminType }}::class;
    }

    public function getPossibleChildTypes(): array
    {
        return [];
    }

    public function getPagePartAdminConfigurations(): array
    {
        return [
{% for section in sections %}
            '{{ section }}',
{% endfor %}
        ];
    }

    public function getPageTemplates(): array
    {
        return ['{{ template }}'];
    }

    public function getDefaultView(): string
    {
        return 'Pages/Common/view.html.twig';
    }
