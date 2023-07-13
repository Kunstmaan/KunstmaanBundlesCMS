<?php

namespace Kunstmaan\UtilitiesBundle\Tests\Helper\Cipher;

use Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher;
use PHPUnit\Framework\TestCase;

/**
 * UrlSafeCipherTest
 */
class UrlSafeCipherTest extends TestCase
{
    public const SECRET = 'secret';
    public const CONTENT = 'This is a random sentence which will be encrypted and then decrypted!';

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
        $this->assertNotSame(self::CONTENT, $encryptedValue);
        $decryptedValue = $this->cipher->decrypt($encryptedValue);
        $this->assertSame(self::CONTENT, $decryptedValue);
    }
}
