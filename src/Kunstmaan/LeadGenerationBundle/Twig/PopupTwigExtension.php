<?php

namespace Kunstmaan\LeadGenerationBundle\Twig;

use Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup;
use Kunstmaan\LeadGenerationBundle\Entity\Rule\AbstractRule;
use Kunstmaan\LeadGenerationBundle\Service\PopupManager;
use Kunstmaan\LeadGenerationBundle\Service\RuleServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PopupTwigExtension extends \Twig_Extension
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
     * @param PopupManager $popupManager
     * @param ContainerInterface $container
     * @param array $popupTypes
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
        return array(
            new \Twig_SimpleFunction('lead_generation_render_js_includes', array($this, 'renderJsIncludes'), array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('lead_generation_render_popups_html', array($this, 'renderPopupsHtml'), array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('lead_generation_render_initialize_js', array($this, 'renderInitializeJs'), array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('lead_generation_get_rule_properties', array($this, 'getRuleProperties')),
            new \Twig_SimpleFunction('get_available_popup_types', array($this, 'getAvailablePopupTypes')),
            new \Twig_SimpleFunction('get_available_rule_types', array($this, 'getAvailableRuleTypes')),
        );
    }

    /**
     * @return string
     */
    public function renderJsIncludes(\Twig_Environment $environment)
    {
        $files = $this->popupManager->getUniqueJsIncludes();

        return $environment->render('KunstmaanLeadGenerationBundle::js-includes.html.twig', array('files' => $files));
    }

    /**
     * @return string
     */
    public function renderPopupsHtml(\Twig_Environment $environment)
    {
        $popups = $this->popupManager->getPopups();

        return $environment->render('KunstmaanLeadGenerationBundle::popups-html.html.twig', array('popups' => $popups));
    }

    /**
     * @return string
     */
    public function renderInitializeJs(\Twig_Environment $environment)
    {
        $popups = $this->popupManager->getPopups();

        return $environment->render('KunstmaanLeadGenerationBundle::initialize-js.html.twig', array('popups' => $popups, 'debug' => $this->debug));
    }

    /**
     * @param AbstractRule $rule
     * @return array
     */
    public function getRuleProperties(AbstractRule $rule)
    {
        $properties = array();
        if (!is_null($rule->getService())) {
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
        $popups = array();
        foreach($this->popupTypes as $popupType) {
            $object = new $popupType();
            $popups[$object->getClassname()] = $object->getFullClassname();
        }

        return $popups;
    }

    /**
     * Get the available popup types for a specific popup.
     *
     * @param AbstractPopup $popup
     * @return array
     */
    public function getAvailableRuleTypes(AbstractPopup $popup)
    {
        $rulesTypes = $this->popupManager->getAvailableRules($popup);

        $rules = array();
        foreach($rulesTypes as $ruleType) {
            $object = new $ruleType();
            $rules[$object->getClassname()] = $object->getFullClassname();
        }

        return $rules;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kunstmaan_lead_generation_popup_twig_extension';
    }
}
