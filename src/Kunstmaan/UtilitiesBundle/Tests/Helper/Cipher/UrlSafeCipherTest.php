<?php

namespace Kunstmaan\UtilitiesBundle\Tests\Helper\Cipher;

use Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher;
use PHPUnit\Framework\TestCase;

/**
 * UrlSafeCipherTest
 */
class UrlSafeCipherTest extends TestCase
{
    const SECRET = 'secret';
    const CONTENT = 'This is a random sentence which will be encrypted and then decrypted!';

    /**
     * @var UrlSafeCipher
     */
    protected $cipher;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @covers \Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher::__construct
     */
    protected function setUp(): void
    {
        $this->cipher = new UrlSafeCipher(self::SECRET);
    }

    /**
     * @covers \Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher::encrypt
     * @covers \Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher::decrypt
     */
    public function testEncryptDecrypt()
    {
        $encryptedValue = $this->cipher->encrypt(self::CONTENT);
        $this->assertNotEquals(self::CONTENT, $encryptedValue);
        $decryptedValue = $this->cipher->decrypt($encryptedValue);
        $this->assertEquals(self::CONTENT, $decryptedValue);
    }

    /**
     * @group legacy
     * @covers \Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher::hex2bin
     */
    public function testHex2bin()
    {
        $hexValue = bin2hex(self::CONTENT);
        $this->assertNotEquals(self::CONTENT, $hexValue);
        $binValue = $this->cipher->hex2bin($hexValue);
        $this->assertEquals(self::CONTENT, $binValue);
    }

    /**
     * @group legacy
     * @expectedDeprecation Overriding the "Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher::hex2bin" method is deprecated since KunstmaanUtilitiesBundle 5.5 and the method will be removed in KunstmaanUtilitiesBundle 6.0. The "Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher" class will use the native hex2bin function instead.
     * @expectedDeprecation The "Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher::hex2bin" method is deprecated since KunstmaanUtilitiesBundle 5.5 and will be removed in KunstmaanUtilitiesBundle 6.0.
     */
    public function testHex2binOverride()
    {
        $encryptedLetter = '6465663530323030326666656464313233623237656563366361666136306139303637663530626436623665356463326134363162326564646339643565353738643631633336663637626562316535353732393337643561656437653464373163643631383134613636303766626231366462376430393530616332346634636264636663663436333730333132303138303561393166616466616562366163613030366561643664';
        $decryptedLetter = 'a';
        $cipherOveride = new class(self::SECRET) extends UrlSafeCipher {
            public function hex2bin($hexString)
            {
                return parent::hex2bin($hexString);
            }
        };

        $decryptedResult = $cipherOveride->decrypt($encryptedLetter);

        $this->assertEquals($decryptedLetter, $decryptedResult);
    }
}
