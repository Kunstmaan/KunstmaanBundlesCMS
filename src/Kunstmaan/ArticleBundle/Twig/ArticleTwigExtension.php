<?php

namespace Kunstmaan\ArticleBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Extension for article bundle.
 *
 * @final since 5.4
 */
class ArticleTwigExtension extends AbstractExtension
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(EntityManagerInterface $em, RouterInterface $router)
    {
        $this->em = $em;
        $this->router = $router;

        if (\func_num_args() > 2) {
            @trigger_error(sprintf('Passing the "request_stack" service as the third argument in "%s" is deprecated in KunstmaanArticleBundle 5.1 and will be removed in KunstmaanArticleBundle 6.0. Remove the "request_stack" argument from your service definition.', __METHOD__), E_USER_DEPRECATED);
        }
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'get_article_tag_path', [$this, 'getArticleTagRouterPath']
            ),
            new TwigFunction(
                'get_article_category_path', [$this, 'getArticleCategoryRouterPath']
            ),
            new TwigFunction(
                'get_article_categories', [$this, 'getCategories']
            ),
            new TwigFunction(
                'get_article_tags', [$this, 'getTags']
            ),
        ];
    }

    /**
     * Get tags array for view.
     *
     * @param string $className
     *
     * @return array
     */
    public function getTags(Request $request, $className)
    {
        $context = [];

        $tagRepository = $this->em->getRepository($className);
        $context['tags'] = $tagRepository->findBy([], ['name' => 'ASC']);

        $searchTag = $request->get('tag') ? explode(',', $request->get('tag')) : null;
        if ($searchTag) {
            $context['activeTag'] = true;
            $context['activeTags'] = $searchTag;
        }

        return $context;
    }

    /**
     * Get categories array for view.
     *
     * @param string $className
     *
     * @return array
     */
    public function getCategories(Request $request, $className)
    {
        $context = [];

        $categoryRepository = $this->em->getRepository($className);
        $context['categories'] = $categoryRepository->findBy([], ['name' => 'ASC']);

        $searchCategory = $request->get('category') ? explode(',', $request->get('category')) : null;
        if ($searchCategory) {
            $context['activeCategory'] = true;
            $context['activeCategories'] = $searchCategory;
        }

        return $context;
    }

    /**
     * @param string $slug
     * @param string $tag
     * @param string $locale
     * @param array  $parameters
     * @param int    $referenceType
     *
     * @return string
     */
    public function getArticleTagRouterPath($slug, $tag, $locale, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $routeName = sprintf('_slug_tag_%s', $locale);

        return $this->getArticleRouterPath($routeName, 'tag', $slug, $tag, $locale, $parameters, $referenceType);
    }

    /**
     * @param string $slug
     * @param string $category
     * @param string $locale
     * @param array  $parameters
     * @param int    $referenceType
     *
     * @return string
     */
    public function getArticleCategoryRouterPath($slug, $category, $locale, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $routeName = sprintf('_slug_category_%s', $locale);

        return $this->getArticleRouterPath($routeName, 'category', $slug, $category, $locale, $parameters, $referenceType);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'article_twig_extension';
    }

    /**
     * @param string $routeName
     * @param string $type
     * @param string $slug
     * @param string $tagOrCategory
     * @param string $locale
     * @param array  $parameters
     * @param int    $referenceType
     *
     * @return string
     */
    protected function getArticleRouterPath($routeName, $type, $slug, $tagOrCategory, $locale, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        if (!$this->articleRouteExists($type, $locale)) {
            $routeName = '_slug';
        }
        if (!isset($parameters[$type])) {
            $parameters[$type] = $tagOrCategory;
        }
        if (!isset($parameters['url'])) {
            $parameters['url'] = $slug;
        }
        if (!isset($parameters['_locale'])) {
            $parameters['_locale'] = $locale;
        }

        return $this->router->generate($routeName, $parameters, $referenceType);
    }

    /**
     * @param string $type
     * @param string $locale
     *
     * @return bool
     */
    protected function articleRouteExists($type, $locale)
    {
        $routeName = sprintf('_slug_%s_%s', $type, $locale);

        try {
            return !\is_null($this->router->getRouteCollection()->get($routeName));
        } catch (\Exception $e) {
            return false;
        }
    }
}
