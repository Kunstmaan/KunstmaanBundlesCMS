<?php

namespace Kunstmaan\MediaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ExtensionConstraint extends Constraint
{
    /**
     * @var array
     */
    public $whitelistedExtensions = [];

    /**
     * @var array
     */
    public $blacklistedExtensions = [];

    public function __construct($options = null)
    {
        parent::__construct($options);

        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    public function validatedBy(): string
    {
        return ExtensionValidator::class;
    }

    private function setOptions(array $options): void
    {
        $whitelistedExtensions = $options['whitelistedExtensions'] ?? [];

        if (is_array($whitelistedExtensions)) {
            $this->whitelistedExtensions = $whitelistedExtensions;
        }

        $blacklistedExtensions = $options['blacklistedExtensions'] ?? [];

        if (is_array($blacklistedExtensions)) {
            $this->blacklistedExtensions = $blacklistedExtensions;
        }
    }
}
