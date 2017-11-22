<?php

namespace Kunstmaan\UtilitiesBundle\Helper\Cipher;

use Webmozart\Assert\Assert;

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
     * @var array $replacements
     */
    private static $replacements = [
        ['+', '/', '='],
        ['_', '-', '.']
    ];

    /**
     * @param string $secret
     * @throws \InvalidArgumentException
     */
    public function __construct($secret)
    {
        if (empty($secret)) {
            throw new \InvalidArgumentException("You need to configure a Cipher secret in your parameters.yml before you can use this!");
        }
        $this->secret = md5($secret);
    }

    /**
     * Encrypt the given value to an unreadable string.
     *
     * @param string $value
     *
     * @return string
     * @throws \RuntimeException
     */
    public function encrypt($value)
    {
        Assert::stringNotEmpty($value);

        $isSourceStrong = true;
        $iv_length = openssl_cipher_iv_length(self::CIPHER_METHOD);
        $iv = openssl_random_pseudo_bytes($iv_length, $isSourceStrong);
        if (false === $isSourceStrong || false === $iv) {
            throw new \RuntimeException('IV generation failed');
        }
        $value = openssl_encrypt($iv.$value, self::CIPHER_METHOD, $this->secret, 0, $iv);
        return str_replace(self::$replacements[0], self::$replacements[1], $value);
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
        $value = base64_decode(str_replace(self::$replacements[1], self::$replacements[0], $value));
        Assert::stringNotEmpty($value);

        $iv_length = openssl_cipher_iv_length(self::CIPHER_METHOD);
        $body_data = base64_encode(substr($value, $iv_length));
        $iv = substr($value, 0, $iv_length);
        return openssl_decrypt($body_data, self::CIPHER_METHOD, $this->secret, 0, $iv);
    }

}
