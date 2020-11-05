<?php

namespace Kunstmaan\LeadGenerationBundle\Twig;

use Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup;
use Kunstmaan\LeadGenerationBundle\Entity\Rule\AbstractRule;
use Kunstmaan\LeadGenerationBundle\Service\PopupManager;
use Kunstmaan\LeadGenerationBundle\Service\RuleServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @final since 5.4
 */
class PopupTwigExtension extends AbstractExtension
{
    /**
     * @var PopupManager
     */
    private $popupManager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $popupTypes;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param bool $debug
     */
    public function __construct(PopupManager $popupManager, ContainerInterface $container, array $popupTypes, $debug)
    {
        $this->popupManager = $popupManager;
        $this->container = $container;
        $this->popupTypes = $popupTypes;
        $this->debug = $debug;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('lead_generation_render_js_includes', [$this, 'renderJsIncludes'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('lead_generation_render_popups_html', [$this, 'renderPopupsHtml'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('lead_generation_render_initialize_js', [$this, 'renderInitializeJs'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('lead_generation_get_rule_properties', [$this, 'getRuleProperties']),
            new TwigFunction('get_available_popup_types', [$this, 'getAvailablePopupTypes']),
            new TwigFunction('get_available_rule_types', [$this, 'getAvailableRuleTypes']),
        ];
    }

    /**
     * @return string
     */
    public function renderJsIncludes(Environment $environment)
    {
        $files = $this->popupManager->getUniqueJsIncludes();

        return $environment->render('@KunstmaanLeadGeneration/js-includes.html.twig', ['files' => $files]);
    }

    /**
     * @return string
     */
    public function renderPopupsHtml(Environment $environment)
    {
        $popups = $this->popupManager->getPopups();

        return $environment->render('@KunstmaanLeadGeneration/popups-html.html.twig', ['popups' => $popups]);
    }

    /**
     * @return string
     */
    public function renderInitializeJs(Environment $environment)
    {
        $popups = $this->popupManager->getPopups();

        return $environment->render('@KunstmaanLeadGeneration/initialize-js.html.twig', ['popups' => $popups, 'debug' => $this->debug]);
    }

    /**
     * @return array
     */
    public function getRuleProperties(AbstractRule $rule)
    {
        $properties = [];
        if (!\is_null($rule->getService())) {
            $service = $this->container->get($rule->getService());
            if ($service instanceof RuleServiceInterface) {
                $properties = $service->getJsProperties($rule);
            }
        }

        return array_merge($rule->getJsProperties(), $properties);
    }

    /**
     * Get the available popup types.
     *
     * @return array
     */
    public function getAvailablePopupTypes()
    {
        $popups = [];
        foreach ($this->popupTypes as $popupType) {
            $object = new $popupType();
            $popups[$object->getClassname()] = $object->getFullClassname();
        }

        return $popups;
    }

    /**
     * Get the available popup types for a specific popup.
     *
     * @return array
     */
    public function getAvailableRuleTypes(AbstractPopup $popup)
    {
        $rulesTypes = $this->popupManager->getAvailableRules($popup);

        $rules = [];
        foreach ($rulesTypes as $ruleType) {
            $object = new $ruleType();
            $rules[$object->getClassname()] = $object->getFullClassname();
        }

        return $rules;
    }
}
