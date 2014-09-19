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
     * @return {{ adminType }}
     */
    public function getDefaultAdminType()
    {
        return new {{ adminType }}();
    }