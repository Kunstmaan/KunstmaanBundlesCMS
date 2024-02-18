<?php

declare(strict_types=1);

namespace Kunstmaan\AdminBundle\Helper\AdminPanel;

final class AdminPanelLogoutAction implements AdminPanelActionInterface
{
    /** @var string */
    private $logoutUrl;
    /** @var string|null */
    private $icon;
    /** @var string */
    private $label;
    /** @var string */
    private $template = '@KunstmaanAdmin/AdminPanel/_admin_panel_logout_action.html.twig';

    public function __construct(string $url, string $label, ?string $icon = null, ?string $template = null)
    {
        $this->logoutUrl = $url;
        $this->label = $label;
        $this->icon = $icon;
        if (!empty($template)) {
            $this->template = $template;
        }
    }

    public function getLogoutUrl(): string
    {
        return $this->logoutUrl;
    }

    public function getUrl(): array
    {
        return [];
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }
}
