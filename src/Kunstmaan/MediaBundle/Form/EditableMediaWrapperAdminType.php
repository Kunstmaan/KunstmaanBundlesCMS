<?php

namespace Kunstmaan\MediaBundle\Form;

use Kunstmaan\MediaBundle\Entity\EditableMediaWrapper;
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @experimental This feature is experimental and is a subject to change, be advised when using this feature and classes.
 */
final class EditableMediaWrapperAdminType extends AbstractType
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

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $croppingViewGroup = $options['cropping_views_group'];
        $selectedCroppingViews = $this->croppingViews[self::DEFAULT];
        $useFocusPoint = false;
        $useCropper = true;
        $focusPointClasses = $this->croppingViews[self::FOCUS_POINT_CLASSES];
        if ($croppingViewGroup !== self::DEFAULT && isset($this->croppingViews[self::CUSTOM_VIEWS][$croppingViewGroup]['views'])) {
            $selectedCroppingViews = $this->croppingViews[self::CUSTOM_VIEWS][$croppingViewGroup]['views'];
            $useFocusPoint = $this->croppingViews[self::CUSTOM_VIEWS][$croppingViewGroup]['use_focus_point'] ?? false;
            $useCropper = $this->croppingViews[self::CUSTOM_VIEWS][$croppingViewGroup]['use_cropping'] ?? true;
        }
        $builder->add('media', MediaType::class, [
            'label' => false,
            'mediatype' => 'image',
            'show_image_edit_modal' => true,
            'use_focus_point' => $useFocusPoint,
            'use_cropping' => $useCropper,
            'focus_point_classes' => json_encode($focusPointClasses),
            'cropping_views' => json_encode($selectedCroppingViews),
        ]);
        $builder->add('runTimeConfig', HiddenType::class, [
            'label' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => EditableMediaWrapper::class,
                'cropping_views_group' => self::DEFAULT,
            ]
        );
        $groups = array_keys($this->croppingViews[self::CUSTOM_VIEWS]);
        $groups[] = self::DEFAULT;
        $resolver->setAllowedValues('cropping_views_group', $groups);
    }
}
