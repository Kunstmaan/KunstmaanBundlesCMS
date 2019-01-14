<?php

namespace Kunstmaan\ArticleBundle\Router;

use Kunstmaan\NodeBundle\Router\SlugRouter;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Translation\TranslatorInterface;

class TagCategoryRouter extends SlugRouter
{
    /**
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRouteCollection()
    {
        if (!is_null($this->routeCollection)) {
            return $this->routeCollection;
        }
        $this->routeCollection = new RouteCollection();

        $extendParameters = array('category' => null, 'tag' => null);
        $baseSlugParameters = array_merge($this->getSlugRouteParameters(), $extendParameters);
        $baseSlugPreviewParameters = array_merge($this->getPreviewRouteParameters(), $extendParameters);

        /** @var TranslatorInterface $translator */
        $translator = $this->container->get('translator');

        if ($this->isMultiLanguage()) {
            foreach ($this->getFrontendLocales() as $locale) {
                $categoryTrans = $translator->trans('article_overview_page.route.category', [], null, $locale);
                $tagTrans = $translator->trans('article_overview_page.route.tag', [], null, $locale);

                $routePathParts = array(
                    '_slug_category_tag' => sprintf('/%s/{category}/%s/{tag}', $categoryTrans, $tagTrans),
                    '_slug_tag' => sprintf('/%s/{tag}', $tagTrans),
                    '_slug_category' => sprintf('/%s/{category}', $categoryTrans),
                );

                foreach ($routePathParts as $routeName => $routePart) {
                    $slugParameters = $baseSlugParameters;
                    $slugParameters['path'] = '/{_locale}/{url}' . $routePart;

                    $slugPreviewParameters = $baseSlugPreviewParameters;
                    $slugPreviewParameters['path'] = '/{_locale}/admin/preview/{url}' . $routePart;

                    $routeName .= '_' . $locale;
                    $this->addRoute($routeName . '_preview', $slugPreviewParameters);
                    $this->addRoute($routeName, $slugParameters);
                }
            }
        } else {
            $categoryTrans = $translator->trans('article_overview_page.route.category');
            $tagTrans = $translator->trans('article_overview_page.route.tag');

            $slugParameters = $baseSlugParameters;
            $slugPreviewParameters = $baseSlugPreviewParameters;

            $routePathParts = array(
                '_slug_category_tag' => sprintf('/%s/{category}/%s/{tag}', $categoryTrans, $tagTrans),
                '_slug_tag' => sprintf('/%s/{tag}', $tagTrans),
                '_slug_category' => sprintf('/%s/{category}', $categoryTrans),
            );

            foreach ($routePathParts as $routeName => $routePart) {
                $slugParameters['path'] = '/{url}' . $routePart;
                $slugPreviewParameters['path'] = '/admin/preview/{url}' . $routePart;

                $this->addRoute($routeName . '_preview', $slugPreviewParameters);
                $this->addRoute($routeName, $slugParameters);
            }
        }

        return $this->routeCollection;
    }
}
