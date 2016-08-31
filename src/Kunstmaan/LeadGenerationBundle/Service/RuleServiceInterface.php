<?php

namespace Kunstmaan\LeadGenerationBundle\Service;

use Kunstmaan\LeadGenerationBundle\Entity\Rule\AbstractRule;

interface RuleServiceInterface
{
    /**
     * @param AbstractRule $rule
     *
     * @return array
     */
    public function getJsProperties(AbstractRule $rule);
}
