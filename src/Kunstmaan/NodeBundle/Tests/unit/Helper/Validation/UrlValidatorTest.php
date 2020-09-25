<?php

namespace Kunstmaan\NodeBundle\Tests\Helper;

use Kunstmaan\NodeBundle\Validation\URLValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UrlValidatorTest extends TestCase
{
    /**
     * @var URLValidator|MockObject
     */
    private $validatorMock;

    protected function setUp(): void
    {
        $this->validatorMock = $this->getMockForTrait(URLValidator::class);
    }

    /**
     * @dataProvider emailAddressProvider
     */
    public function testIsEmailAddress($email, $result)
    {
        $this->assertSame($result, $this->validatorMock->isEmailAddress($email));
    }

    /**
     * @dataProvider internalLinkProvider
     */
    public function testIsInternalLink($link, $result)
    {
        $this->assertSame($result, $this->validatorMock->isInternalLink($link));
    }

    /**
     * @dataProvider internalMediaLinkProvider
     */
    public function testIsInternalMediaLink($link, $result)
    {
        $this->assertSame($result, $this->validatorMock->isInternalMediaLink($link));
    }

    public function emailAddressProvider()
    {
        return [
            ['abc', false],
            ['test@example.com', 'test@example.com'],
            ['test@local', false],
        ];
    }

    public function internalLinkProvider()
    {
        return [
            ['[NT:123]', false],
            ['[NT123]', true],
            ['[NTABC]', false],
            ['[NT123ABC]', false],
            ['[host_a:NT123]', true],
            ['http://www.google.com', false],
            ['[NT123][M20]', true],
        ];
    }

    public function internalMediaLinkProvider()
    {
        return [
            ['[M:123]', false],
            ['[M123]', true],
            ['[MABC]', false],
            ['[M123ABC]', false],
            ['[host_a:M23]', true],
            ['http://www.google.com', false],
            ['[M123][NT20]', true],
        ];
    }
}
