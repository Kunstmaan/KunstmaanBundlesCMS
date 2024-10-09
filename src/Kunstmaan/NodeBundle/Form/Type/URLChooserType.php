<?php

declare(strict_types=1);

namespace Kunstmaan\NodeBundle\Form\Type;

use Kunstmaan\NodeBundle\Form\DataTransformer\URLChooserToLinkTransformer;
use Kunstmaan\NodeBundle\Form\EventListener\URLChooserFormSubscriber;
use Kunstmaan\NodeBundle\Form\EventListener\URLChooserLinkTypeSubscriber;
use Kunstmaan\NodeBundle\Validator\Constraint\ValidExternalUrl;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\When;

/**
 * URLChooserType
 */
class URLChooserType extends AbstractType
{
    const INTERNAL = 'internal';

    const EXTERNAL = 'external';

    const EMAIL = 'email';

    public function __construct(private bool $improvedUrlChooser = false)
    {
    }

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = [
            'pagepart.link.internal' => self::INTERNAL,
            'pagepart.link.external' => self::EXTERNAL,
            'pagepart.link.email' => self::EMAIL,
        ];

        if ($types = $options['link_types']) {
            foreach ($choices as $key => $choice) {
                if (!\in_array($choice, $types, false)) {
                    unset($choices[$key]);
                }
            }
        }

        $builder->addViewTransformer(new URLChooserToLinkTransformer($this->improvedUrlChooser));
        if (!$this->improvedUrlChooser) {
            $builder->add(
                'link_type',
                ChoiceType::class,
                [
                    'required' => true,
                    'mapped' => false,
                    'attr' => [
                        'class' => 'js-change-link-type',
                    ],
                    'choices' => $choices,
                ]
            );
            $builder->get('link_type')->addEventSubscriber(new URLChooserLinkTypeSubscriber());
            $builder->addEventSubscriber(new URLChooserFormSubscriber());

            return;
        }

        $builder->add('link_url', HiddenType::class, [
            'required' => false,
        ]);
        $builder->add('link_type', URLTypeType::class, [
            'required' => true,
            'choices' => array_flip($choices),
            'attr' => [
                'class' => 'urlchooser-type',
            ],
        ]);

        $builder->add('choice_external', TextType::class, [
            'attr' => ['placeholder' => 'https://'],
            'constraints' => [
                new When($this->getLinkTypeExpression(self::EXTERNAL), [
                    new ValidExternalUrl(),
                ]),
            ],
            'error_bubbling' => true,
        ]);
        $builder->add('choice_email', EmailType::class, [
            'constraints' => [
                new When($this->getLinkTypeExpression(self::EMAIL), [
                    new Email(),
                ]),
            ],
            'error_bubbling' => true,
        ]);
        $builder->add('choice_internal', InternalURLSelectorType::class);
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver the resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => null,
                'link_types' => [],
                'error_bubbling' => false,
            ]
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['improved_url_chooser'] = $this->improvedUrlChooser;
    }

    private function getLinkTypeExpression(string $type): string
    {
        return 'this.getParent().get("link_type").getData() === "' . $type . '"';
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'urlchooser';
    }
}
