<?php

namespace Kunstmaan\AdminBundle\FlashMessages;

/**
 * Enabled flash types.
 */
class FlashTypes
{
    const SUCCESS = 'success';
    /**
     * @deprecated The `FlashTypes::ERROR` constant is deprecated in KunstmaanAdminBundle 5.4 and will be removed in KunstmaanAdminBundle 6.0. Use `FlashTypes::DANGER` instead.
     */
    const ERROR = 'error';
    const WARNING = 'warning';

    const INFO = 'info';
    const DANGER = 'danger';
}
