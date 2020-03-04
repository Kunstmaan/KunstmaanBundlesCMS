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
            '{% if not isV4 %}{{ bundle }}:{%endif%}{{ section }}',
{% endfor %}
        ];
    }

    public function getPageTemplates(): array
    {
        return ['{% if not isV4 %}{{ bundle }}:{%endif%}{{ template }}'];
    }

    public function getDefaultView(): string
    {
        return '{% if not isV4 %}{{ bundle }}:{%endif%}Pages/Common{% if not isV4 %}:{% else %}/{% endif %}view.html.twig';
    }
