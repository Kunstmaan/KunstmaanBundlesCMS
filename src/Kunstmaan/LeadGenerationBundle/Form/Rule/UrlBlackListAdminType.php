<?php

namespace Kunstmaan\LeadGenerationBundle\Form\Rule;

use Symfony\Component\Form\FormBuilderInterface;

class UrlBlackListAdminType extends AbstractRuleAdminType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('urls', 'textarea', array(
            'attr' => array('info_text' => 'Define a list of blacklist url (patterns).
                                            Each url on a separate line.
                                            You can also use regex expressions.
                                            Examples: /blog/articles; /blog/page[0-9]+; /blog/.*/comments; ^/$')
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'url_blacklist_form';
    }
}
