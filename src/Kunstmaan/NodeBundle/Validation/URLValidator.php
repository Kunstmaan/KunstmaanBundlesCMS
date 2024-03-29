<?php

namespace Kunstmaan\NodeBundle\Validation;

trait URLValidator
{
    /**
     * Check if given text is e-mail address.
     */
    public function isEmailAddress($link)
    {
        return filter_var($link, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Check if given text is an internal link.
     */
    public function isInternalLink($link)
    {
        if (null === $link) {
            return false;
        }

        preg_match_all("/\[(([a-z_A-Z\.]+):)?NT(\d+)\]/", $link, $matches, PREG_SET_ORDER);

        return \count($matches) > 0;
    }

    /**
     * Check if given text is an internal media link.
     */
    public function isInternalMediaLink($link)
    {
        if (null === $link) {
            return false;
        }

        preg_match_all("/\[(([a-z_A-Z]+):)?M(\d+)\]/", $link, $matches, PREG_SET_ORDER);

        return \count($matches) > 0;
    }
}
