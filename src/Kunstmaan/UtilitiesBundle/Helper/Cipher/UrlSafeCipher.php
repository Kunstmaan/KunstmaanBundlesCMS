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
     * @param bool   $raw_binary
     *
     * @return string
     *
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function encrypt($value, $raw_binary = false)
    {
        return bin2hex(parent::encrypt($value, $raw_binary));
    }

    /**
     * Decrypt the given value so that it's readable again.
     *
     * @param string $value
     * @param bool   $raw_binary
     *
     * @return string
     *
     * @throws \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function decrypt($value, $raw_binary = false)
    {
        return parent::decrypt(hex2bin($value), $raw_binary);
    }
}
