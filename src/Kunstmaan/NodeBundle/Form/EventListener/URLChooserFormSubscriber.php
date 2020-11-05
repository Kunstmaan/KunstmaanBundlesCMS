<?php

namespace Kunstmaan\NodeBundle\Form\EventListener;

use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Kunstmaan\NodeBundle\Validation\URLValidator;
use Kunstmaan\NodeBundle\Validator\Constraint\ValidExternalUrl;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Url;

class URLChooserFormSubscriber implements EventSubscriberInterface
{
    use URLValidator;

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * When opening the form for the first time, check the type of URL and set the according fields.
     */
    public function postSetData(FormEvent $event)
    {
        $this->formModifier($event);
    }

    public function preSubmit(FormEvent $event)
    {
        $this->formModifier($event);
    }

    private function formModifier(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $form->getData();

        $constraints = [];
        $attributes['class'] = 'js-change-urlchooser';

        if (!empty($data) && $form->has('link_type')) {
            // Check if e-mail address
            if ($this->isEmailAddress($data)) {
                $form->get('link_type')->setData(URLChooserType::EMAIL);
                $constraints[] = new Email();
            } // Check if internal link
            elseif ($this->isInternalLink($data) || $this->isInternalMediaLink($data)) {
                $form->get('link_type')->setData(URLChooserType::INTERNAL);
                $attributes['choose_url'] = true;
            } // Else, it's an external link
            else {
                $form->get('link_type')->setData(URLChooserType::EXTERNAL);
                $constraints[] = new ValidExternalUrl();
            }
        } else {
            $choices = $form->get('link_type')->getConfig()->getOption('choices');
            $firstOption = array_shift($choices);

            switch ($firstOption) {
                case URLChooserType::INTERNAL:
                    $attributes['choose_url'] = true;

                    break;
                case URLChooserType::EXTERNAL:
                    $attributes['placeholder'] = 'https://';
                    $constraints[] = new ValidExternalUrl();

                    break;
                case URLChooserType::EMAIL:
                    $constraints[] = new Email();

                    break;
            }

            $form->get('link_type')->setData($firstOption);
        }

        $form->add(
            'link_url',
            TextType::class,
            [
                'label' => 'URL',
                'required' => true,
                'attr' => $attributes,
                'constraints' => $constraints,
                'error_bubbling' => true,
            ]
        );
    }
}
