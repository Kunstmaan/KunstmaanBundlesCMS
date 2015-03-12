    /**
    * Returns the name of this type.
    *
    * @return string The name of this type
    */
    public function getName()
    {
    return '{{ pagepart }}type';
    }

    /**
    * Sets the default options for this type.
    *
    * @param OptionsResolverInterface $resolver The resolver for the options.
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    $resolver->setDefaults(array(
    'data_class' => '\{{ namespace }}\Entity\PageParts\{{ pagepart }}'
    ));
    }