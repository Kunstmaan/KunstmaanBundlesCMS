<?php

namespace Kunstmaan\MediaBundle\Form;

use Kunstmaan\MediaBundle\Entity\CroppableMediaLink;
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CroppableMediaLinkAdminType extends AbstractType
{
    /** @var array */
    private $croppingViews;

    public function __construct(array $croppingViews)
    {
        $this->croppingViews = $croppingViews;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('media', MediaType::class, [
            'label' => 'mediapagepart.image.choosefile',
            'show_cropper_modal' => true,
            'mediatype' => 'image',
        ]);
        $builder->add('runTimeConfig', HiddenType::class, [
            'label' => false,
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'croppable_media_link';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => CroppableMediaLink::class,
                'cropping_views' => ['desktop', 'media'],
            ]
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $selectedCroppingViews = [];
        foreach($options['cropping_views'] as $cropping_view) {
            if(isset($this->croppingViews[$cropping_view])) {
                $selectedCroppingViews[] = $this->croppingViews[$cropping_view];
            }
        }
        $view->vars['cropping_views'] = json_encode($selectedCroppingViews);
    }
}
