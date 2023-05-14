<?php

namespace Kunstmaan\LeadGenerationBundle\Form;

use Kunstmaan\LeadGenerationBundle\Form\Popup\AbstractPopupAdminType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewsletterSubscriptionType extends AbstractPopupAdminType
{
    /**
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', EmailType::class, [
            'label' => 'kuma_lead_generation.form.newsletter_subscription.email.label',
            'constraints' => [
                new NotBlank(),
                new Email(['checkMX' => false]),
            ],
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'newslettersubscription_form';
    }
}
