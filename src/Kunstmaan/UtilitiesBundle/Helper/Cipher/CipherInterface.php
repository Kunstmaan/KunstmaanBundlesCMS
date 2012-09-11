<?php

namespace Kunstmaan\UtilitiesBundle\Helper\Cipher;

interface CipherInterface
{

    /**
     * Encrypt the given value to an unreadable string.
     *
     * @param string $value
     * @return string
     */
    public function encrypt($value);

    /**
     * Decrypt the given value so that it's readable again.
     *
     * @param string $value
     * @return string
     */
    public function decrypt($value);

    /**
     * Encrypt the given value so that it's unreadable and that it can be used in an url.
     *
     * @param string $value
     * @return string
     */
    public function urlSafeEncrypt($value);

    /**
     * Decrypt the given value so that it's readable again.
     *
     * @param string $value
     * @return string
     */
    public function urlSafeDecrypt($value);

}
