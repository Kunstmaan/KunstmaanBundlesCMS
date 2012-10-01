<?php

namespace Kunstmaan\UtilitiesBundle\Helper\Cipher;

/**
 * Cipher, this class can be used to encrypt and decrypt string values.
 */
class Cipher implements CipherInterface
{

    /**
     * @var string $secret
     */
    private $secret;

    /**
     * @param string $secret
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    /**
     * Encrypt the given value to an unreadable string.
     *
     * @param string $value
     *
     * @return string
     */
    public function encrypt($value)
    {
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($this->secret), $value, MCRYPT_MODE_CBC, md5(md5($this->secret))));
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
        return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($this->secret), base64_decode($value), MCRYPT_MODE_CBC, md5(md5($this->secret))), "\0");
    }

}
