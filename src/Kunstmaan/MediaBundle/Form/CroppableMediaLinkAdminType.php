<?php

namespace Kunstmaan\MediaBundle\Form;

use Kunstmaan\MediaBundle\Entity\CroppableMediaLink;
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CroppableMediaLinkAdminType extends AbstractType
{
    const DEFAULT = 'default';

    /** @var array */
    private $croppingViews;

    public function __construct(array $croppingViews)
    {
        $this->croppingViews = $croppingViews;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $croppingViewGroup = $options['cropping_views_group'];
        $selectedCroppingViews = $this->croppingViews[self::DEFAULT];
        if ($croppingViewGroup !== self::DEFAULT && isset($this->croppingViews['custom_views'][$croppingViewGroup]['views'])) {
            $selectedCroppingViews = $this->croppingViews['custom_views'][$croppingViewGroup]['views'];
        }
        $builder->add('media', MediaType::class, [
            'label' => 'mediapagepart.image.choosefile',
            'mediatype' => 'image',
            'show_cropper_modal' => true,
            'cropping_views' => json_encode($selectedCroppingViews),
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
                'cropping_views_group' => self::DEFAULT,
            ]
        );
    }
}
