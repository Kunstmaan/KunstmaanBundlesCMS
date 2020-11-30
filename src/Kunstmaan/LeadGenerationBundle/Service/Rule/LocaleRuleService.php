<?php

namespace Kunstmaan\LeadGenerationBundle\Service\Rule;

use Kunstmaan\LeadGenerationBundle\Entity\Rule\AbstractRule;
use Kunstmaan\LeadGenerationBundle\Service\RuleServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class LocaleRuleService implements RuleServiceInterface
{
    /** @var Request */
    private $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @return array
     */
    public function getJsProperties(AbstractRule $rule)
    {
        return [
            'requestlocale' => $this->request->getLocale(),
        ];
    }
}
