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

/**
 * HTML5 range type field
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
