<?php
/**
 * Created by Kunstmaan.
 * Date: 20/06/14
 * Time: 15:25
 */

namespace Kunstmaan\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;

trigger_deprecation('kunstmaan/admin-bundle', '6.2', 'The "%s" class is deprecated and will be removed in 7.0. Use "%s" instead.', RangeType::class, \Symfony\Component\Form\Extension\Core\Type\RangeType::class);

/**
 * HTML5 range type field
 *
 * @deprecated since KunstmaanAdminBundle 6.2 and will be removed in KunstmaanAdminBundle 7.0. Use "Symfony\Component\Form\Extension\Core\Type\RangeType" instead.
 */
class RangeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['attr' => ['min' => 0, 'max' => 100, 'step' => 1]]);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return IntegerType::class;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'range';
    }
}
