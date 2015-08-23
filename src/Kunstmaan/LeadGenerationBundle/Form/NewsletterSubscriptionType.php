<?php

namespace Kunstmaan\LeadGenerationBundle\Form;

use Kunstmaan\LeadGenerationBundle\Form\Popup\AbstractPopupAdminType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

/**
 * The type for NewsletterSubscription
 */
class NewsletterSubscriptionType extends AbstractPopupAdminType
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
        $builder->add('email', 'email', array(
            'constraints' => array(
                new NotBlank(),
                new Email(array('checkMX' => false)),
            ),
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'newslettersubscription_form';
    }
}
