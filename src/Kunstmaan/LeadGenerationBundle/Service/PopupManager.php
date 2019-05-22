<?php

namespace Kunstmaan\LeadGenerationBundle\Service;

use Doctrine\ORM\EntityManager;
use Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup;
use Kunstmaan\LeadGenerationBundle\Entity\Rule\AfterXSecondsRule;
use Kunstmaan\LeadGenerationBundle\Entity\Rule\AfterXScrollPercentRule;
use Kunstmaan\LeadGenerationBundle\Entity\Rule\MaxXTimesRule;
use Kunstmaan\LeadGenerationBundle\Entity\Rule\RecurringEveryXTimeRule;
use Kunstmaan\LeadGenerationBundle\Entity\Rule\UrlBlacklistRule;
use Kunstmaan\LeadGenerationBundle\Entity\Rule\UrlWhitelistRule;
use Kunstmaan\LeadGenerationBundle\Entity\Rule\OnExitIntentRule;

class PopupManager
{
    /**
     * @var array|null
     */
    private $popups = null;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Get a list of popup definitions.
     *
     * @return array
     */
    public function getPopups()
    {
        if (\is_null($this->popups)) {
            $this->popups = $this->em->getRepository('KunstmaanLeadGenerationBundle:Popup\AbstractPopup')->findAll();
        }

        return $this->popups;
    }

    /**
     * Get a list of unique javascript files that should be included in the html.
     *
     * @return array
     */
    public function getUniqueJsIncludes()
    {
        $includes = array();
        foreach ($this->getPopups() as $popup) {
            foreach ($popup->getRules() as $rule) {
                $includes[] = $rule->getJsFilePath();
            }
        }

        return array_unique($includes);
    }

    /**
     * @param AbstractPopup $popup
     *
     * @return array
     */
    public function getAvailableRules(AbstractPopup $popup)
    {
        if (!\is_null($popup->getAvailableRules())) {
            return $popup->getAvailableRules();
        }

        return array(
            AfterXSecondsRule::class,
            AfterXScrollPercentRule::class,
            MaxXTimesRule::class,
            RecurringEveryXTimeRule::class,
            UrlBlacklistRule::class,
            UrlWhitelistRule::class,
            OnExitIntentRule::class,
        );
    }
}
