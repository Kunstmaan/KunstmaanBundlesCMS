<?php

/*
 * This file is part of the KunstmaanBundlesCMS package.
 *
 * (c) Kunstmaan <https://github.com/Kunstmaan/KunstmaanBundlesCMS/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kunstmaan\Rest\NodeBundle\Service\Transformers;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\PagePartConfigurationReader\PagePartConfigurationReader;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplateConfigurationService;
use Kunstmaan\Rest\CoreBundle\Service\Transformers\TransformerInterface;
use Kunstmaan\Rest\NodeBundle\Model\ApiContext;
use Kunstmaan\Rest\NodeBundle\Model\ApiPage;
use Kunstmaan\Rest\NodeBundle\Model\ApiPagePart;
use Kunstmaan\Rest\NodeBundle\Model\ApiPageTemplate;

/**
 * Class PageTemplateTransformer
 */
class PageTemplateTransformer implements TransformerInterface
{
    /** @var EntityManager */
    private $em;

    /** @var PageTemplateConfigurationService */
    private $pageTemplateConfigurationService;

    /** @var PagePartConfigurationReader */
    private $pagePartConfigurationReader;

    /**
     * PageTemplateTransformer constructor.
     *
     * @param EntityManager                    $em
     * @param PageTemplateConfigurationService $pageTemplateConfigurationService
     * @param PagePartConfigurationReader      $pagePartConfigurationReader
     */
    public function __construct(
        EntityManager $em,
        PageTemplateConfigurationService $pageTemplateConfigurationService,
        PagePartConfigurationReader $pagePartConfigurationReader
    ) {
        $this->em = $em;
        $this->pageTemplateConfigurationService = $pageTemplateConfigurationService;
        $this->pagePartConfigurationReader = $pagePartConfigurationReader;
    }

    /**
     * This function will determine if the DataTransformer is eligible for transformation
     *
     * @param $object
     *
     * @return bool
     */
    public function canTransform($object)
    {
        return $object instanceof ApiPage;
    }

    /**
     * @param ApiPage $apiPage
     *
     * @return ApiPage
     */
    public function transform($apiPage)
    {
        $page = $apiPage->getPage()->getData();

        if (!$page instanceof HasPagePartsInterface) {
            return $apiPage;
        }

        $pageTemplate = $this->pageTemplateConfigurationService->findOrCreateFor($page);

        $apiPageTemplate = new ApiPageTemplate();
        $apiPageTemplate->setName($pageTemplate->getPageTemplate());

        $contexts = $this->pagePartConfigurationReader->getPagePartContexts($page);

        $apiContexts = new ArrayCollection();
        foreach ($contexts as $context) {
            $apiContext = new ApiContext();
            $apiContext->setName($context);

            $apiContext->setPageParts($this->getPagePartsForContext($page, $context));

            $apiContexts->add($apiContext);
        }

        $apiPageTemplate->setContexts($apiContexts);

        $apiPage->setPageTemplate($apiPageTemplate);

        return $apiPage;
    }

    /**
     * @param EntityInterface $page
     * @param string          $context
     *
     * @return ArrayCollection
     */
    protected function getPagePartsForContext(EntityInterface $page, $context)
    {
        $entityRepository = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef');
        $pageparts = $entityRepository->getPageParts($page, $context);

        $apiPageParts = new ArrayCollection();
        foreach ($pageparts as $pagepart) {
            $apiPagePart = new ApiPagePart();
            $apiPagePart->setData($pagepart);
            $apiPageParts->add($apiPagePart);
        }

        return $apiPageParts;
    }
}
