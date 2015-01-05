<?php

namespace Kunstmaan\NodeBundle\Form\Type;

use Symfony\Component\Form\FormInterface;

use Symfony\Component\Form\FormView;

use Symfony\Component\Form\AbstractType;
use Kunstmaan\UtilitiesBundle\Helper\Slugifier;

/**
 * Sype
 */
class SlugType extends AbstractType
{

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
        return 'slug';
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
            $nodeTranslation = $form->getParent()->getData();
            $view->vars['reset'] = Slugifier::slugify($nodeTranslation->getTitle(), '');
            $parentNode = $nodeTranslation->getNode()->getParent();
            if ($parentNode !== null) {
                $nodeTranslation = $parentNode->getNodeTranslation($nodeTranslation->getLang(), true);
                $slug = $nodeTranslation->getSlugPart();
                if (!empty($slug)) {
                    $slug .= '/';
                }
                $view->vars['prefix'] = $slug;
            }
    }
}
