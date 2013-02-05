<?php
namespace Kunstmaan\SearchBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * SearchPageAdminType
 */
class SearchPageAdminType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('title');
  }

  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return 'searchpage';
  }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Kunstmaan\SearchBundle\Entity\SearchPage',
        ));
    }
}

