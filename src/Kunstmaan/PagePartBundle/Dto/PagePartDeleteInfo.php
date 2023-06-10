<?php

namespace Kunstmaan\PagePartBundle\Dto;

/**
 * @internal
 */
final class PagePartDeleteInfo
{
    private string $name;
    private string $id;
    private ?PagePartDeleteInfo $nestedDeleteInfo;

    public function __construct(string $name, string $id, ?self $nestedDeleteInfo)
    {
        $this->name = $name;
        $this->id = $id;
        $this->nestedDeleteInfo = $nestedDeleteInfo;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNestedDeleteInfo(): ?PagePartDeleteInfo
    {
        return $this->nestedDeleteInfo;
    }

    public function hasNestedDeleteInfo(): bool
    {
        return null !== $this->nestedDeleteInfo;
    }
}
