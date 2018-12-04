<?php

namespace Kunstmaan\AdminBundle\Tests\Helper;

use Doctrine\Common\Cache\Cache;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker;
use Kunstmaan\TranslatorBundle\Service\Translator\Translator;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Kernel;

class FakeClient extends Client
{
    /**
     * @return \Psr\Http\Message\ResponseInterface|void
     *
     * @throws Exception
     */
    public function post()
    {
        throw new Exception('bang!');
    }
}

class VersionCheckTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var VersionChecker
     */
    protected $object;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Cache
     */
    protected $cache;

    public function setUp()
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->expects($this->any())->method('getParameter')->will($this->onConsecutiveCalls('https://nasa.gov', 123, true));
        $this->cache = $this->createMock(Cache::class);
        $this->object = new VersionChecker($this->container, $this->cache);
    }

    /**
     * @throws \Kunstmaan\AdminBundle\Helper\VersionCheck\Exception\ParseException
     */
    public function testVersionChecker()
    {
        $currentPath = getcwd();
        if (strpos($currentPath, 'src') !== false) {
            $currentPath = strstr($currentPath, 'src', true);
        }
        $path = realpath($currentPath.'/src');
        $trans = $this->createMock(Translator::class);
        $kernel = $this->createMock(Kernel::class);
        $request = $this->createMock(Request::class);
        $stack = $this->createMock(RequestStack::class);
        $trans->expects($this->any())->method('trans')->willReturn('algo en una differente idioma');
        $stack->expects($this->once())->method('getCurrentRequest')->willReturn($request);
        $kernel->expects($this->exactly(2))->method('getRootDir')->willReturn($path);
        $request->expects($this->once())->method('getHttpHost')->willReturn('https://nasa.gov');
        $this->cache->expects($this->once())->method('fetch')->willReturn('not_an_array');
        $this->container->expects($this->any())->method('get')->will(
            $this->onConsecutiveCalls($stack, $kernel, $trans, $kernel)
        );

        $object = $this->object;
        $this->assertTrue($object->isEnabled());
        $object->periodicallyCheck();
    }

    /**
     * @throws \Kunstmaan\AdminBundle\Helper\VersionCheck\Exception\ParseException
     */
    public function testPeriodicCheckReturnsNothingWhenDisabled()
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->expects($this->any())->method('getParameter')->will($this->onConsecutiveCalls('https://nasa.gov', 123, false, false));
        $this->cache = $this->createMock(Cache::class);
        $this->object = new VersionChecker($this->container, $this->cache);
        $this->cache->expects($this->never())->method('fetch');
        $this->object->periodicallyCheck();
        $this->object->check();
    }

    /**
     * @throws \Kunstmaan\AdminBundle\Helper\VersionCheck\Exception\ParseException
     */
    public function testCheckReturnsFalseOnException()
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->expects($this->any())->method('getParameter')->will($this->onConsecutiveCalls('https://nasa.gov', 123, true, true));
        $this->cache = $this->createMock(Cache::class);
        $this->object = new VersionChecker($this->container, $this->cache);
        $this->cache->expects($this->never())->method('fetch');
        $currentPath = getcwd();
        if (strpos($currentPath, 'src') !== false) {
            $currentPath = strstr($currentPath, 'src', true);
        }
        $path = realpath($currentPath.'/src');
        $client = new FakeClient();
        $this->object->setClient($client);
        $trans = $this->createMock(Translator::class);
        $kernel = $this->createMock(Kernel::class);
        $request = $this->createMock(Request::class);
        $stack = $this->createMock(RequestStack::class);
        $trans->expects($this->any())->method('trans')->willReturn('algo en una differente idioma');
        $stack->expects($this->once())->method('getCurrentRequest')->willReturn($request);
        $kernel->expects($this->exactly(2))->method('getRootDir')->willReturn($path);
        $this->container->expects($this->any())->method('get')->will(
            $this->onConsecutiveCalls($stack, $kernel, $trans, $kernel)
        );
        $this->assertFalse($this->object->check());
    }

    /**
     * @throws \Kunstmaan\AdminBundle\Helper\VersionCheck\Exception\ParseException
     */
    public function testGetData()
    {
        $mock = new MockHandler([new Response(200, ['Content-Type: application/json'], '{"fake": "data"}')]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $this->object->setClient($client);
        $this->cache->expects($this->once())->method('fetch')->willReturn('not_an_array');
        $this->cache->expects($this->once())->method('save')->willReturn(true);
        $currentPath = getcwd();
        if (strpos($currentPath, 'src') !== false) {
            $currentPath = strstr($currentPath, 'src', true);
        }
        $path = realpath($currentPath.'/src');
        $trans = $this->createMock(Translator::class);
        $kernel = $this->createMock(Kernel::class);
        $request = $this->createMock(Request::class);
        $stack = $this->createMock(RequestStack::class);
        $trans->expects($this->any())->method('trans')->willReturn('algo en una differente idioma');
        $stack->expects($this->once())->method('getCurrentRequest')->willReturn($request);
        $kernel->expects($this->exactly(2))->method('getRootDir')->willReturn($path);
        $this->container->expects($this->any())->method('get')->will(
            $this->onConsecutiveCalls($stack, $kernel, $trans, $kernel)
        );
        $this->object->periodicallyCheck();
    }

    /**
     * @throws \Kunstmaan\AdminBundle\Helper\VersionCheck\Exception\ParseException
     * @throws \ReflectionException
     */
    public function testCheckGetPackagesThrowsException()
    {
        $this->expectException(Exception::class);
        $trans = $this->createMock(Translator::class);
        $kernel = $this->createMock(Kernel::class);
        $trans->expects($this->any())->method('trans')->willReturn('algo en una differente idioma');
        $this->container->expects($this->any())->method('get')->will(
            $this->onConsecutiveCalls($trans, $kernel)
        );

        $mirror = new ReflectionClass(VersionChecker::class);
        $method = $mirror->getMethod('getPackages');
        $method->setAccessible(true);
        $method->invoke($this->object);
    }

    /**
     * @throws \Kunstmaan\AdminBundle\Helper\VersionCheck\Exception\ParseException
     * @throws \ReflectionException
     */
    public function testCheckGetPackagesThrowsExceptionWhenNoPackagesInLock()
    {
        $currentPath = getcwd();
        if (strpos($currentPath, 'src') !== false) {
            $currentPath = strstr($currentPath, 'src', true);
        }
        $path = realpath($currentPath.'/src/Kunstmaan/AdminBundle/Tests/Helper/VersionCheck');
        $this->expectException(Exception::class);
        $trans = $this->createMock(Translator::class);
        $kernel = $this->createMock(Kernel::class);
        $trans->expects($this->any())->method('trans')->willReturn('algo en una differente idioma');
        $kernel->expects($this->once())->method('getRootDir')->willReturn($path);
        $this->container->expects($this->any())->method('get')->will(
            $this->onConsecutiveCalls($trans, $kernel)
        );

        $mirror = new ReflectionClass(VersionChecker::class);
        $method = $mirror->getMethod('getPackages');
        $method->setAccessible(true);
        $method->invoke($this->object);
    }

    /**
     * @throws \Kunstmaan\AdminBundle\Helper\VersionCheck\Exception\ParseException
     * @throws \ReflectionException
     */
    public function testCheckGetPackagesThrowsExceptionWithBadJson()
    {
        $currentPath = getcwd();
        if (strpos($currentPath, 'src') !== false) {
            $currentPath = strstr($currentPath, 'src', true);
        }
        $path = realpath($currentPath.'/src/Kunstmaan/AdminBundle/Tests/Helper');
        $this->expectException(Exception::class);
        $trans = $this->createMock(Translator::class);
        $kernel = $this->createMock(Kernel::class);
        $trans->expects($this->any())->method('trans')->willReturn('algo en una differente idioma');
        $kernel->expects($this->once())->method('getRootDir')->willReturn($path);
        $this->container->expects($this->any())->method('get')->will(
            $this->onConsecutiveCalls($trans, $kernel)
        );

        $mirror = new ReflectionClass(VersionChecker::class);
        $method = $mirror->getMethod('getPackages');
        $method->setAccessible(true);
        $method->invoke($this->object);
    }
}
