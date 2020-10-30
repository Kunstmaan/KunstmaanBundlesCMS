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
        $reflector = new \ReflectionMethod($this, 'hex2bin');
        $methodOverride = ($reflector->getDeclaringClass()->getName() !== __CLASS__);

        //NEXT_MAJOR: Remove override check, remove hex2bin method and switch to native php function
        $binaryValue = hex2bin($value);
        if ($methodOverride) {
            @trigger_error(sprintf('Overriding the "%s::hex2bin" method is deprecated since KunstmaanUtilitiesBundle 5.5 and the method will be removed in KunstmaanUtilitiesBundle 6.0. The "%s" class will use the native hex2bin function instead.', __CLASS__, __CLASS__), E_USER_DEPRECATED);

            $binaryValue = $this->hex2bin($value);
        }

        return parent::decrypt($binaryValue, $raw_binary);
    }

    /**
     * Decodes a hexadecimal encoded binary string.
     * PHP version >= 5.4 has a function for this by default.
     *
     * @deprecated The "Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher::hex2bin" method is deprecated since KunstmaanUtilitiesBundle 5.5 and will be removed in KunstmaanUtilitiesBundle 6.0.
     *
     * @param string $hexString
     *
     * @return string
     */
    public function hex2bin($hexString)
    {
        @trigger_error(sprintf('The "%s::hex2bin" method is deprecated since KunstmaanUtilitiesBundle 5.5 and will be removed in KunstmaanUtilitiesBundle 6.0.', __CLASS__), E_USER_DEPRECATED);

        $pos = 0;
        $result = '';
        while ($pos < \strlen($hexString)) {
            if (strpos(" \t\n\r", $hexString[$pos]) !== false) {
                ++$pos;
            } else {
                $code = hexdec(substr($hexString, $pos, 2));
                $pos += 2;
                $result .= \chr($code);
            }
        }

        return $result;
    }
}
