<?php

namespace Kunstmaan\LeadGenerationBundle\Service\Rule;

use Kunstmaan\LeadGenerationBundle\Entity\Rule\AbstractRule;
use Kunstmaan\LeadGenerationBundle\Service\RuleServiceInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class LocaleRuleService implements RuleServiceInterface
{
    /** @var Request $request */
    private $request;

    /**
     * LocaleRuleService constructor.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @param AbstractRule $rule
     * @return array
     */
    public function getJsProperties(AbstractRule $rule)
    {
        return array(
            'requestlocale' => $this->request->getLocale(),
        );
    }

}
