<?php

namespace Kunstmaan\MediaBundle\Form\Type;

use Kunstmaan\MediaBundle\Helper\IconFont\IconFontManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * IconFontType
 */
class IconFontType extends AbstractType
{
    /**
     * @var IconFontManager
     */
    private $iconFontManager;

    /**
     * @param IconFontManager $iconFontManager
     */
    public function __construct(IconFontManager $iconFontManager)
    {
        $this->iconFontManager = $iconFontManager;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'iconfont';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'loader'      => null,
                'loader_data' => null
            )
        );
    }

    // BC for SF < 2.7
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['loader']) {
            $loader = $this->iconFontManager->getDefaultLoader();
        } else {
            $loader = $this->iconFontManager->getLoader($options['loader']);
        }
        $loader->setData($options['loader_data']);

        $builder->setAttribute('loader', $options['loader']);
        $builder->setAttribute('loader_object', $loader);
        $builder->setAttribute('loader_data', $options['loader_data']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['loader']        = $form->getConfig()->getAttribute('loader');
        $view->vars['loader_object'] = $form->getConfig()->getAttribute('loader_object');
        $view->vars['loader_data']   = serialize($form->getConfig()->getAttribute('loader_data'));
    }
}
