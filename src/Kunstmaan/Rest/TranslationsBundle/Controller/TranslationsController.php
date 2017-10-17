<?php

namespace Kunstmaan\Rest\TranslationsBundle\Controller;

use DateTime;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Kunstmaan\Rest\TranslationsBundle\Model\Exception\TranslationException;
use Kunstmaan\Rest\TranslationsBundle\Service\TranslationService;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class TranslationsController.
 *
 * @SWG\Definition(
 *   definition="singleTranslation",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           @SWG\Property(property="keyword",type="string"),
 *           @SWG\Property(property="text",type="string"),
 *       )
 *   }
 * )
 * @SWG\Definition(
 *   definition="listTranslation",
 *   type="array",
 *   allOf={
 *       @SWG\Schema(
 *             @SWG\Items(ref="#/definitions/singleTranslation")
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="putTranslation",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"keyword", "text", "locale", "domain"},
 *           @SWG\Property(property="keyword",type="string"),
 *           @SWG\Property(property="text",type="string"),
 *           @SWG\Property(property="locale",type="string"),
 *           @SWG\Property(property="domain",type="string", example="messages"),
 *       )
 *   }
 * )
 * @SWG\Definition(
 *   definition="postTranslation",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(ref="#/definitions/putTranslation"),
 *   }
 * )
 * @SWG\Definition(
 *   definition="postTranslations",
 *   type="array",
 *   allOf={
 *       @SWG\Schema(
 *             @SWG\Items(ref="#/definitions/postTranslation")
 *       )
 *   }
 * )
 * @SWG\Definition(
 *   definition="keywordCollection",
 *   type="array",
 *   allOf={
 *       @SWG\Schema(
 *           @SWG\Items(ref="#/definitions/deprecateKeyword")
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="deprecateKeyword",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"keyword"},
 *           @SWG\Property(property="keyword",type="string", example="keyword")
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="disablingDate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"date"},
 *           @SWG\Property(property="date",type="datetime", example="2048-05-12")
 *       )
 *   }
 * )
 */
class TranslationsController extends FOSRestController
{
    /**
     * @View(
     *     statusCode=200
     * )
     *
     * @Rest\QueryParam(name="locale", nullable=false, description="locale")
     * @Rest\Get("/public/translations")
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return array
     *
     *
     * * @SWG\Get(
     *     path="/api/public/translations",
     *     description="Get a list of all translations",
     *     operationId="getTranslations",
     *     produces={"application/json"},
     *     tags={"translations"},
     *     @SWG\Parameter(
     *         name="locale",
     *         in="query",
     *         type="string",
     *         description="the locale of the languages you want",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when successful",
     *         @SWG\Schema(ref="#/definitions/listTranslation")
     *     ),
     *     @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     *         @SWG\Schema(
     *             ref="#/definitions/ErrorModel"
     *         )
     *     )
     * )
     */
    public function getTranslationsAction(ParamFetcherInterface $paramFetcher)
    {
        $locale = $paramFetcher->get('locale');

        if (!$locale) {
            throw new NotFoundHttpException('locale is required');
        }

        $translations = $this->getDoctrine()->getRepository('KunstmaanTranslatorBundle:Translation')
            ->findAllNotDisabled($locale);
        return $translations;
    }

    /**
     * @View(
     *     statusCode=200
     * )
     *
     * @Rest\QueryParam(name="locale", nullable=false, description="locale")
     * @Rest\Get("/public/translations/{keyword}")
     *
     * @param string                $keyword
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Translation
     *
     *
     * @SWG\Get(
     *     path="/api/public/translations/{keyword}",
     *     description="Get a translation",
     *     operationId="getTranslation",
     *     produces={"application/json"},
     *     tags={"translations"},
     *     @SWG\Parameter(
     *         name="locale",
     *         in="query",
     *         type="string",
     *         description="the locale of the languages you want",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="keyword",
     *         in="path",
     *         type="string",
     *         description="the keyword of the translation you want",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when successful",
     *         @SWG\Schema(ref="#/definitions/singleTranslation")
     *     ),
     *     @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     *         @SWG\Schema(
     *             ref="#/definitions/ErrorModel"
     *         )
     *     )
     * )
     */
    public function getTranslationAction($keyword, ParamFetcherInterface $paramFetcher)
    {
        $locale = $paramFetcher->get('locale');

        if (!$locale) {
            throw new NotFoundHttpException('locale is required');
        }

        /** @var Translation $translation */
        $translation = $this->getDoctrine()
            ->getRepository(Translation::class)
            ->findOneBy(['locale' => $locale, 'keyword' => $keyword]);

        if ($translation && !$translation->isDisabled()) {
            return $translation;
        }

        throw new NotFoundHttpException();
    }

    /**
     * @View(
     *     statusCode=200
     * )
     *
     * @Rest\Post("/translations")
     *
     * @param Request $request
     *
     * @return array
     *
     *
     * @SWG\Post(
     *     path="/api/translations",
     *     description="Create multiple translations",
     *     operationId="createTranslation",
     *     produces={"application/json"},
     *     tags={"translations"},
     *     @SWG\Parameter(
     *         name="translation",
     *         in="body",
     *         required=true,
     *         type="single",
     *         description="The posted translations",
     *         @SWG\Schema(ref="#/definitions/postTranslations"),
     *     ),
     *     @SWG\Parameter(
     *         name="X-KUMA-API-KEY",
     *         in="header",
     *         type="string",
     *         description="The authentication access token",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Returned when successfully created",
     *         @SWG\Schema(ref="#/definitions/listTranslation")
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Returned when no translations are provided",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     ),
     *     @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     *         @SWG\Schema(
     *             ref="#/definitions/ErrorModel"
     *         )
     *     )
     * )
     */
    public function postTranslationsAction(Request $request)
    {
        /** @var TranslationService $translationCreator */
        $translationCreator = $this->get(TranslationService::class);
        $json = $request->getContent();
        $translations = json_decode($json, true);

        $translations = $translationCreator->createCollectionFromArray($translations);
        foreach ($translations as $translation) {
            $translationCreator->createOrUpdateTranslation($translation);
        }

        return $translations;
    }

    /**
     * @View(
     *     statusCode=200
     * )
     *
     * @Rest\Put("/translations/deprecate")
     *
     * @SWG\Put(
     *     path="/api/translations/deprecate",
     *     description="deprecate translations by keyword",
     *     operationId="deprecateTranslation",
     *     produces={"application/json"},
     *     tags={"translations"},
     *     @SWG\Parameter(
     *         name="deprecatedTranslation",
     *         in="body",
     *         required=true,
     *         type="single",
     *         description="The posted translations",
     *         @SWG\Schema(ref="#/definitions/keywordCollection"),
     *     ),
     *     @SWG\Parameter(
     *         name="X-KUMA-API-KEY",
     *         in="header",
     *         type="string",
     *         description="The authentication access token",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Returned when successfully deprecated"
     *     ),
     *     @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     *         @SWG\Schema(
     *             ref="#/definitions/ErrorModel"
     *         )
     *     )
     * )
     */
    public function deprecateTranslationsAction(Request $request)
    {
        /** @var TranslationService $translationCreator */
        $translationCreator = $this->get(TranslationService::class);

        $json = $request->getContent();
        $keywords = json_decode($json, true);

        foreach ($keywords as $keyword) {
            if (!array_key_exists('keyword', $keyword)) {
                throw new TranslationException(TranslationException::NOT_VALID);
            }
        }

        foreach ($keywords as $keyword) {
            $translationCreator->deprecateTranslations($keyword['keyword']);
        }
    }

    /**
     * @View(
     *     statusCode=200
     * )
     *
     * @Rest\Put("/translations/disable")
     *
     * @SWG\Put(
     *     path="/api/translations/disable",
     *     description="disable translations by keyword",
     *     operationId="disableTranslation",
     *     produces={"application/json"},
     *     tags={"translations"},
     *     @SWG\Parameter(
     *         name="disabledTranslation",
     *         in="body",
     *         required=true,
     *         type="single",
     *         description="The posted translations",
     *         @SWG\Schema(ref="#/definitions/disablingDate"),
     *     ),
     *     @SWG\Parameter(
     *         name="X-KUMA-API-KEY",
     *         in="header",
     *         type="string",
     *         description="The authentication access token",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Returned when successfully disabled"
     *     ),
     *     @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     *         @SWG\Schema(
     *             ref="#/definitions/ErrorModel"
     *         )
     *     )
     * )
     */
    public function disableDeprecatedTranslationsAction(Request $request)
    {
        /** @var TranslationService $translationCreator */
        $translationCreator = $this->get(TranslationService::class);

        $json = $request->getContent();
        $translationDeprecation = json_decode($json, true);

        if (!array_key_exists('date', $translationDeprecation)) {
            throw new TranslationException(TranslationException::NOT_VALID);
        }

        $translationCreator->disableDeprecatedTranslations(new DateTime($translationDeprecation['date']));
    }

    /**
     * @param Request $request
     *
     * @throws TranslationException
     *
     *
     * @Rest\Put("/translations/enable")
     *
     * @SWG\Put(
     *     path="/api/translations/enable",
     *     description="re-enable translations by keyword",
     *     operationId="enableTranslation",
     *     produces={"application/json"},
     *     tags={"translations"},
     *     @SWG\Parameter(
     *         name="enabledTranslation",
     *         in="body",
     *         required=true,
     *         type="single",
     *         description="The posted translations",
     *         @SWG\Schema(ref="#/definitions/keywordCollection"),
     *     ),
     *     @SWG\Parameter(
     *         name="X-KUMA-API-KEY",
     *         in="header",
     *         type="string",
     *         description="The authentication access token",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Returned when successfully enabled"
     *     ),
     *     @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     *         @SWG\Schema(
     *             ref="#/definitions/ErrorModel"
     *         )
     *     )
     * )
     */
    public function enableDeprecatedTranslationsAction(Request $request)
    {
        /** @var TranslationService $translationCreator */
        $translationCreator = $this->get(TranslationService::class);

        $json = $request->getContent();
        $keywords = json_decode($json, true);

        foreach ($keywords as $keyword) {
            if (!array_key_exists('keyword', $keyword)) {
                throw new TranslationException(TranslationException::NOT_VALID);
            }
        }

        foreach ($keywords as $keyword) {
            $translationCreator->enableDeprecatedTranslations($keyword['keyword']);
        }
    }
}
