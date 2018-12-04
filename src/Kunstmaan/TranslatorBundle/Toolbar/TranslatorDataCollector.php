<?php

namespace Kunstmaan\TranslatorBundle\Toolbar;

use Kunstmaan\AdminBundle\Helper\Toolbar\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TranslatorDataCollector extends AbstractDataCollector
{
    /**
     * @var DataCollectorTranslator
     */
    private $translator;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * TranslatorDataCollector constructor.
     *
     * @param DataCollectorTranslator $translator
     * @param UrlGeneratorInterface   $urlGenerator
     */
    public function __construct(DataCollectorTranslator $translator, UrlGeneratorInterface $urlGenerator)
    {
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @return array
     */
    public function getAccessRoles()
    {
        return ['ROLE_ADMIN'];
    }

    /**
     * @return array
     */
    public function collectData()
    {
        $route = $this->urlGenerator->generate('KunstmaanTranslatorBundle_settings_translations');

        $options = [
            'filter_columnname' => [
                'keyword',
            ],
            'filter_uniquefilterid' => [
                1,
            ],
            'filter_comparator_1' => 'equals',
            'filter' => 'filter',
        ];

        $translations = [];

        foreach ($this->translator->getCollectedMessages() as $message) {
            if ($message['state'] !== DataCollectorTranslator::MESSAGE_EQUALS_FALLBACK && !empty($message['id'])) {
                $options['filter_value_1'] = $message['id'];
                $translations[$message['id']] = [
                    'id' => $message['id'],
                    'message' => $message['translation'],
                    'route' => $this->urlGenerator->generate('KunstmaanTranslatorBundle_settings_translations', $options),
                ];
            }
        }

        $data = [
            'route' => $route,
            'translations' => $translations,
        ];

        return ['data' => $data];
    }

    /**
     * @param Request         $request
     * @param Response        $response
     * @param \Exception|null $exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        if (!$this->showDataCollection($request, $response) || !$this->isEnabled()) {
            $this->data = false;
        } else {
            $this->data = $this->collectData();
        }
    }

    /**
     * Gets the data for template
     *
     * @return array The request events
     */
    public function getTemplateData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kuma_translation';
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    public function reset()
    {
        $this->data = [];
    }
}
