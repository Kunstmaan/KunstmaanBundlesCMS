<?php

/*
 * This file is part of the KunstmaanBundlesCMS package.
 *
 * (c) Kunstmaan <https://github.com/Kunstmaan/KunstmaanBundlesCMS/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kunstmaan\Rest\NodeBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\ControllerTrait;
use FOS\RestBundle\Request\ParamFetcher;
use Kunstmaan\Rest\CoreBundle\Controller\AbstractApiController;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\Rest\CoreBundle\Helper\DataTransformerTrait;
use Kunstmaan\Rest\CoreBundle\Service\DataTransformerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swagger\Annotations as SWG;
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
class PagesController extends AbstractApiController
{
    use ControllerTrait;
    use DataTransformerTrait;

    /** @var EntityManagerInterface */
    private $em;

    /** @var DataTransformerService */
    private $dataTransformer;

    public function __construct(EntityManagerInterface $em, DataTransformerService $dataTransformer)
    {
        $this->em = $em;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * Retrieve nodes paginated
     *
     * @SWG\Get(
     *     path="/api/pages",
     *     description="Get a pages of a certain type",
     *     operationId="getPages",
     *     produces={"application/json"},
     *     tags={"pages"},
     *     @SWG\Parameter(
     *         name="type",
     *         in="query",
     *         type="string",
     *         description="The FQCN of the page",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="locale",
     *         in="query",
     *         type="string",
     *         description="The language of your content",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when successful",
     *         @SWG\Schema(ref="#/definitions/ApiPage")
     *     ),
     *     @SWG\Response(
     *         response=403,
     *         description="Returned when the user is not authorized to fetch nodes",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     ),
     *     @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     *
     * @param Request $request
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getPagesAction(Request $request, ParamFetcher $paramFetcher)
    {
        if (!$request->query->has('locale')) {
            throw new HttpException(400, "Missing locale");
        }

        // TODO: validate query params
        $locale = $request->query->get('locale');
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 2);

        $qb = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')->getOnlineNodeTranslationsQueryBuilder($locale);

        $paginatedCollection = $this->createORMPaginatedCollection($qb, $page, $limit, $this->createTransformerDecorator());

        return $this->handleView($this->view($paginatedCollection, Response::HTTP_OK));
    }

    /**
     * Retrieve nodes paginated
     *
     * @SWG\Get(
     *     path="/api/pages/{id}",
     *     description="Get a page of a certain type by Node ID",
     *     operationId="getPage",
     *     produces={"application/json"},
     *     tags={"pages"},
     *     @SWG\Parameter(
     *         name="type",
     *         in="query",
     *         type="string",
     *         description="The FQCN of the page",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="locale",
     *         in="query",
     *         type="string",
     *         description="The language of your content",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when successful",
     *         @SWG\Schema(ref="#/definitions/ApiPage")
     *     ),
     *     @SWG\Response(
     *         response=403,
     *         description="Returned when the user is not authorized to fetch nodes",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     ),
     *     @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function getPageAction(Request $request, $id)
    {
        if (!$request->query->has('locale')) {
            throw new HttpException(400, "Missing locale");
        }

        $locale = $request->getLocale();

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