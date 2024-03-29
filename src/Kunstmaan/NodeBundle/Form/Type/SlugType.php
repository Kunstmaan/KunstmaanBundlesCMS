<?php

namespace Kunstmaan\NodeBundle\Form\Type;

use Kunstmaan\UtilitiesBundle\Helper\SlugifierInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class SlugType extends AbstractType
{
    /**
     * @var SlugifierInterface
     */
    private $slugifier;

    /**
     * @param SlugifierInterface $slugifier The slugifier
     */
    public function __construct(SlugifierInterface $slugifier)
    {
        $this->slugifier = $slugifier;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'slug';
    }

    /**
     * @return void
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $nodeTranslation = $form->getParent()->getData();
        $view->vars['reset'] = $this->slugifier->slugify($nodeTranslation->getTitle());
        $parentNode = $nodeTranslation->getNode()->getParent();
        $view->vars['prefix'] = '';
        if ($parentNode !== null) {
            $nodeTranslation = $parentNode->getNodeTranslation($nodeTranslation->getLang(), true);
            $slug = $nodeTranslation->getSlugPart();
            if (!empty($slug)) {
                $slug = rtrim($slug, '/') . '/';
            }
            $view->vars['prefix'] = $slug;
        }
    }
}
