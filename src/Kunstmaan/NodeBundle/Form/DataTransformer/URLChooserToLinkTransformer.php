<?php

namespace Kunstmaan\NodeBundle\Form\DataTransformer;

use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Kunstmaan\NodeBundle\Validation\URLValidator;
use Symfony\Component\Form\DataTransformerInterface;

class URLChooserToLinkTransformer implements DataTransformerInterface
{
    use URLValidator;

    public function __construct(private bool $improvedUrlChooser = false)
    {
    }

    public function transform($value): array
    {
        if (!$this->improvedUrlChooser) {
            return [
                'link_type' => $this->getLinkType($value),
                'link_url' => $value,
            ];
        }

        if ($value === null) {
            return [
                'link_type' => URLChooserType::INTERNAL,
                'link_url' => null,
            ];
        }

        $linkType = $this->getLinkType($value);

        return array_merge($this->getChoiceOption($linkType, $value), [
            'link_type' => $linkType,
            'link_url' => $value,
        ]);
    }

    public function reverseTransform($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if ($this->improvedUrlChooser && !empty($value['link_type'])) {
            switch ($value['link_type']) {
                case URLChooserType::INTERNAL:
                    return $value['link_url'];
                case URLChooserType::EXTERNAL:
                    return $value['choice_external'];
                case URLChooserType::EMAIL:
                    return $value['choice_email'];
            }
        }

        return $value['link_url'];
    }

    private function getLinkType(mixed $value): string
    {
        if ($this->isEmailAddress($value)) {
            return URLChooserType::EMAIL;
        }

        if ($this->isInternalLink($value) || $this->isInternalMediaLink($value)) {
            return URLChooserType::INTERNAL;
        }

        return URLChooserType::EXTERNAL;
    }

    private function getChoiceOption(string $linkType, string $value): array
    {
        return match ($linkType) {
            URLChooserType::INTERNAL => ['choice_internal' => ['input' => $value]],
            URLChooserType::EXTERNAL => ['choice_external' => $value],
            URLChooserType::EMAIL => ['choice_email' => $value],
            default => [],
        };
    }
}
