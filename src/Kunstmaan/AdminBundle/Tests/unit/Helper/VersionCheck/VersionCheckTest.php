<?php

namespace Kunstmaan\AdminBundle\Tests\Helper;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Kunstmaan\AdminBundle\Helper\VersionCheck\Exception\ParseException;
use Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Cache\Cache;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Translation\Translator;

class VersionCheckTest extends TestCase
{
    /** @var ContainerInterface (mock) */
    private $container;

    /** @var Cache (mock) */
    private $cache;

    public function setUp()
    {
        /* @var ContainerInterface $container */
        $this->container = $this->createMock(ContainerInterface::class);

        /* @var Cache $cache */
        $this->cache = $this->createMock(Cache::class);
    }

    /**
     * @param array|null $methods
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|VersionChecker
     */
    public function setUpVersionCheckerMock(?array $methods)
    {
        $versionCheckerMock = $this->getMockBuilder(VersionChecker::class)
            ->setConstructorArgs([$this->container, $this->cache])
            ->setMethods($methods)
            ->getMock()
        ;

        return $versionCheckerMock;
    }

    public function testIsEnabled()
    {
        $this->container
            ->expects($this->exactly(3))
            ->method('getParameter')
            ->will($this->onConsecutiveCalls('url', 300, true))
        ;

        $versionCheckerMock = $this->setUpVersionCheckerMock(null);
        $this->assertIsBool($versionCheckerMock->isEnabled());
    }

    public function testPeriodicallyCheck()
    {
        $this->container
            ->expects($this->exactly(3))
            ->method('getParameter')
            ->will($this->onConsecutiveCalls('url', 300, true))
        ;

        $this->cache
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([])
        ;
        $versionCheckerMock = $this->setUpVersionCheckerMock(null);
        $versionCheckerMock->periodicallyCheck();
    }

    public function testCheckWithInvalidResponse()
    {
        $this->container
            ->expects($this->exactly(4))
            ->method('getParameter')
            ->will($this->onConsecutiveCalls('url', 300, true, 'title'))
        ;

        $requestMock = $this->createMock(Request::class);

        $stackMock = $this->createMock(RequestStack::class);
        $stackMock
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($requestMock)
        ;
        $kernelMock = $this->createMock(Kernel::class);

        $this->container
            ->expects($this->exactly(2))
            ->method('get')
            ->will($this->onConsecutiveCalls($stackMock, $kernelMock))
        ;

        $versionCheckerMock = $this->setUpVersionCheckerMock(['parseComposer']);
        $versionCheckerMock
            ->expects($this->once())
            ->method('parseComposer')
            ->willReturn(['name' => 'box/spout'])
        ;
        $this->assertFalse($versionCheckerMock->check());
    }

    /**
     * @dataProvider provider
     */
    public function testCheck(string $lockPath, string $expectedType, string $expected)
    {
        if ('exception' === $expectedType) {
            $this->expectException(ParseException::class);
            $this->expectExceptionMessage($expected);
        }

        $this->container
            ->expects($this->any())
            ->method('getParameter')
            ->will($this->onConsecutiveCalls('url', 300, true, 'title'))
        ;

        $requestMock = $this->createMock(Request::class);

        $stackMock = $this->createMock(RequestStack::class);
        $stackMock
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($requestMock)
        ;

        $translatorMock = $this->createMock(Translator::class);
        $translatorMock
            ->expects($this->any())
            ->method('trans')
            ->willReturn('translated')
        ;

        $kernelMock = $this->createMock(Kernel::class);

        $this->container
            ->expects($this->exactly(3))
            ->method('get')
            ->will($this->onConsecutiveCalls($stackMock, $kernelMock, $translatorMock))
        ;

        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], \json_encode(['foo' => 'bar'])),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $versionCheckerMock = $this->setUpVersionCheckerMock(['getClient', 'getLockPath']);
        $versionCheckerMock
            ->expects($this->any())
            ->method('getClient')
            ->willReturn($client)
        ;
        $versionCheckerMock
            ->expects($this->once())
            ->method('getLockPath')
            ->willReturn($lockPath)
        ;

        if ('instanceOf' === $expectedType) {
            $this->assertInstanceOf($expected, $versionCheckerMock->check());
        } else {
            $versionCheckerMock->check();
        }
    }

    public function provider()
    {
        $baseDir = __DIR__ . '/testdata';

        return [
            'composer.lock ok' => [$baseDir.'/composer_ok.lock', 'instanceOf', \stdClass::class],
            'composer.lock broken' => [$baseDir.'/composer_broken.lock', 'exception', 'translated (#4)'],
            'composer.lock bundleless' => [$baseDir.'/composer_bundleless.lock', 'exception', 'translated'],
            'composer.lock not found' => [$baseDir.'/composer_not_there.lock', 'exception', 'translated'],
        ];
    }
}
