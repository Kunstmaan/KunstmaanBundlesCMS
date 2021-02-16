<?php

namespace Kunstmaan\AdminBundle\Tests\Helper;

use Doctrine\Common\Cache\ArrayCache;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Kunstmaan\AdminBundle\Helper\VersionCheck\Exception\ParseException;
use Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class VersionCheckTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject|LegacyTranslatorInterface|TranslatorInterface */
    private $translator;

    /** @var ContainerInterface (mock) */
    private $container;

    /** @var ArrayAdapter */
    private $cache;

    public function setUp(): void
    {
        /* @var ContainerInterface $container */
        $this->container = $this->createMock(ContainerInterface::class);

        $this->cache = $this->createMock(AdapterInterface::class);

        if (\interface_exists(TranslatorInterface::class)) {
            $this->translator = $this->createMock(TranslatorInterface::class);
        } else {
            $this->translator = $this->createMock(LegacyTranslatorInterface::class);
        }
    }

    /**
     * @group legacy
     * @expectedDeprecation Passing an instance of "Doctrine\Common\Cache\CacheProvider" as the second argument in "Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker::__construct" is deprecated since KunstmaanAdminBundle 5.7 and an instance of "Symfony\Component\Cache\Adapter\AdapterInterface" will be required in KunstmaanAdminBundle 6.0.
     */
    public function testDeprecatedCacheConstructorParameter()
    {
        new VersionChecker($this->createMock(ContainerInterface::class), new ArrayCache(), $this->translator);
    }

    /**
     * @group legacy
     */
    public function testCacheConstructorParameterType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "$cache" parameter should extend from "Doctrine\Common\Cache\CacheProvider" or implement "Symfony\Component\Cache\Adapter\AdapterInterface"');

        new VersionChecker($this->createMock(ContainerInterface::class), new \stdClass(), $this->translator);
    }

    /**
     * @group legacy
     */
    public function testTranslatorConstructorParameterType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "$translator" parameter should be instance of "Symfony\Contracts\Translation\TranslatorInterface" or "Symfony\Component\Translation\TranslatorInterface"');

        new VersionChecker($this->createMock(ContainerInterface::class), new ArrayCache(), new \stdClass());
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|VersionChecker
     */
    public function setUpVersionCheckerMock(?array $methods)
    {
        $versionCheckerMock = $this->getMockBuilder(VersionChecker::class)
            ->setConstructorArgs([$this->container, $this->cache, $this->translator])
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

        $versionChecker = $this->getVersionChecker($this->container, new ArrayAdapter(), $this->translator);

        $this->assertTrue($versionChecker->isEnabled());
    }

    public function testPeriodicallyCheck()
    {
        $this->container
            ->expects($this->exactly(3))
            ->method('getParameter')
            ->will($this->onConsecutiveCalls('url', 300, true))
        ;

        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->method('isHit')->willReturn(true);
        $cacheItem->method('get')->willReturn([]);

        $this->cache
            ->expects($this->once())
            ->method('getItem')
            ->willReturn($cacheItem)
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

        $requestStack = new RequestStack();
        $requestStack->push(new Request());

        $translatorMock = $this->createMock(Translator::class);
        $translatorMock
            ->expects($this->any())
            ->method('trans')
            ->willReturn('translated')
        ;

        $kernelMock = $this->createMock(Kernel::class);

        if ('instanceOf' === $expectedType) {
            $cacheItem = $this->createMock(CacheItemInterface::class);
            $cacheItem->method('isHit')->willReturn(false);
            $cacheItem->expects($this->once())->method('expiresAfter')->with(300);
            $cacheItem->expects($this->once())->method('set')->with($this->isInstanceOf($expected));

            $this->cache
                ->expects($this->once())
                ->method('getItem')
                ->willReturn($cacheItem);
        }

        $this->container
            ->expects($this->exactly(3))
            ->method('get')
            ->will($this->onConsecutiveCalls($requestStack, $kernelMock, $translatorMock))
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
            'composer.lock ok' => [$baseDir . '/composer_ok.lock', 'instanceOf', \stdClass::class],
            'composer.lock broken' => [$baseDir . '/composer_broken.lock', 'exception', 'translated (#4)'],
            'composer.lock bundleless' => [$baseDir . '/composer_bundleless.lock', 'exception', 'translated'],
            'composer.lock not found' => [$baseDir . '/composer_not_there.lock', 'exception', 'translated'],
        ];
    }

    private function getVersionChecker(ContainerInterface $container, AdapterInterface $cache, $translator)
    {
        return new VersionChecker($container, $cache, $translator);
    }
}
