<?php

/*
 * This file is part of the KunstmaanBundlesCMS package.
 *
 * (c) Kunstmaan <https://github.com/Kunstmaan/KunstmaanBundlesCMS/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kunstmaan\ApiBundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\ControllerTrait;
use Kunstmaan\ApiBundle\Model\ApiPage;
use Kunstmaan\ApiBundle\Service\DataTransformerService;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PagesController
 *
 * @author Ruud Denivel <ruud.denivel@kunstmaan.be>
 *
 * @Route(service="kunstmaan_api.controller.pages")
 */
class PagesController
{
    use ControllerTrait;

    /** @var EntityManager */
    private $em;

    /** @var DataTransformerService */
    private $dataTransformer;

    public function __construct(EntityManager $em, DataTransformerService $dataTransformer)
    {
        $this->em = $em;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * Retrieve nodes paginated
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get a page",
     *  resourceDescription="Get a page",
     *  parameters={
     *      {
     *          "name"="type",
     *          "dataType"="string",
     *          "requirement"="\s+",
     *          "required"=true,
     *          "description"="The FQCN of the page"
     *      },
     *      {
     *          "name"="locale",
     *          "dataType"="string",
     *          "required"=true,
     *          "requirement"="\s+",
     *          "description"="The language of your content"
     *      },
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the there are missing required parameters",
     *      403="Returned when the user is not authorized to fetch nodes",
     *      404="Returned when nothing was found for given ID",
     *      500="Something went wrong"
     *  }
     * )
     */
    public function getPagesAction(Request $request, $id)
    {
        if (!$request->query->has('locale')) {
            throw new HttpException(400, "Missing locale");
        }

        $locale = $request->query->get('locale');

        $qb = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')->getOnlineNodeTranslationsQueryBuilder($locale);
        $qb
            ->andWhere('n.id = :nodeId')
            ->setParameter('nodeId', $id)
        ;

        $nodeTranslation = $qb->getQuery()->getOneOrNullResult();
        if (!$nodeTranslation instanceof NodeTranslation) {
            throw new NotFoundHttpException();
        }

        $data = $this->dataTransformer->transform($nodeTranslation);

        return $this->handleView($this->view($data, Response::HTTP_OK));
    }
}