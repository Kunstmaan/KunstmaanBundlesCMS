<?php

namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Exception\FormException;

use Kunstmaan\FormBundle\DataTransformer\OneEntityToIdTransformer;

/**
 * Entity identitifer
 *
 * @author Gregwar <g.passault@gmail.com>
 */
class EntityIdType extends AbstractType
{
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->prependClientTransformer(new OneEntityToIdTransformer(
                        $this->registry->getEntityManager($options['em']),
                        $options['class'],
                        $options['property'],
                        $options['query_builder']
        ));
    }

    public function getDefaultOptions(array $options)
    {
        $defaultOptions = array(
                        'em' => null,
                        'class' => null,
                        'property' => null,
                        'query_builder' => null,
                        'type' => 'hidden',
                        'hidden' => true,
        );

        $options = array_replace($defaultOptions, $options);

        if (null === $options['class']) {
            throw new FormException('You must provide a class option for the entity identifier field');
        }

        return $options;
    }

    public function getParent()
    {
        return $options['hidden'] ? 'hidden' : 'text';
    }

    public function getName()
    {
        return 'entity_id';
    }
}
