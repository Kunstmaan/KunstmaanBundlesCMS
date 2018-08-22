    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
        return '{{ bundle }}:PageParts:{{ pagepart }}/view.html.twig';
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