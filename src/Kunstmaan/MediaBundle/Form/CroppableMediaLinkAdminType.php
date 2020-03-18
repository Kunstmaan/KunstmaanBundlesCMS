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
    private const DEFAULT = 'default';
    private const CUSTOM_VIEWS = 'custom_views';
    private const FOCUS_POINT_CLASSES = 'focus_point_classes';

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
        $useFocusPoint = false;
        $focusPointClasses = $this->croppingViews[self::FOCUS_POINT_CLASSES];
        if ($croppingViewGroup !== self::DEFAULT && isset($this->croppingViews[self::CUSTOM_VIEWS][$croppingViewGroup]['views'])) {
            $selectedCroppingViews = $this->croppingViews[self::CUSTOM_VIEWS][$croppingViewGroup]['views'];
            $useFocusPoint = $this->croppingViews[self::CUSTOM_VIEWS][$croppingViewGroup]['useFocusPoint'] ?? false;
        }
        $builder->add('media', MediaType::class, [
            'label' => 'mediapagepart.image.choosefile',
            'mediatype' => 'image',
            'show_cropper_modal' => true,
            'use_focus_point' => $useFocusPoint,
            'focus_point_classes' => $focusPointClasses,
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
        $groups = array_keys($this->croppingViews[self::CUSTOM_VIEWS]);
        $groups[] = self::DEFAULT;
        $resolver->setAllowedValues('cropping_views_group', $groups);
    }
}
