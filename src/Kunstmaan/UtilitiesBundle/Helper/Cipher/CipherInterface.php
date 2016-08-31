<?php

namespace Kunstmaan\UtilitiesBundle\Helper\Cipher;

/**
 * Cipher interface, classes which extends this interface will make it possible to encrypt and decrypt string values.
 */
interface CipherInterface
{
    /**
     * Encrypt the given value to an unreadable string.
     *
     * @param string $value
     *
     * @return string
     */
    public function encrypt($value);

    /**
     * Decrypt the given value so that it's readable again.
     *
     * @param string $value
     *
     * @return string
     */
    public function decrypt($value);
}
