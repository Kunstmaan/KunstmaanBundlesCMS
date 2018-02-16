<?php

namespace Kunstmaan\UtilitiesBundle\Helper\Cipher;

/**
 * Cipher, this class can be used to encrypt string values to a format which is safe to use in urls.
 */
class UrlSafeCipher extends Cipher
{

    /**
     * Encrypt the given value so that it's unreadable and that it can be used in an url.
     *
     * @param string $value
     *
     * @return string
     */
    public function encrypt($value)
    {
        return bin2hex(parent::encrypt($value));
    }

    /**
     * Decrypt the given value so that it's readable again.
     *
     * @param string $value
     *
     * @return string
     */
    public function decrypt($value)
    {
        return parent::decrypt(hex2bin($value));
    }

    /**
     * Decodes a hexadecimal encoded binary string.
     * PHP version >= 5.4 has a function for this by default.
     *
     * @param String $hexString
     *
     * @return string
     */
    public function hex2bin($hexString)
    {
        return hex2bin($hexString);
    }

}
