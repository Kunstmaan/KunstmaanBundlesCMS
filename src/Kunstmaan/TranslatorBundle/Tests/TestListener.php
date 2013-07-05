<?php
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

class TestListener implements \PHPUnit_Framework_TestListener
{
    public function startTest(PHPUnit_Framework_Test $test)
    {
    }

    public function endTest(PHPUnit_Framework_Test $test, $length)
    {
    }

    protected function _printError($error)
    {

    }

    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
    }

    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
    }

    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
    }

    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
    }

    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        if (strpos($suite->getName(), 'KunstmaanTranslationBundle') === false) {
            return true;
        }

        // include __DIR__.'/app/AppKernel.php';

        $kernel = new \AppKernel('phpunit', true);
        $kernel->boot();

        // drop/create database schema
        $em = $kernel->getContainer()->get('doctrine.orm.default_entity_manager');
        $meta = $em->getMetadataFactory()->getAllMetadata();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $tool->dropSchema($meta);
        $tool->createSchema($meta);

        // insert fixtures
        $fixtures = array(__DIR__.'/files/fixtures.yml');
        $em = $kernel->getContainer()->get('doctrine.orm.default_entity_manager');
        $objects = \Nelmio\Alice\Fixtures::load($fixtures, $em);
        $persister = new \Nelmio\Alice\ORM\Doctrine($em);
        $persister->persist($objects);

    //     if (strpos($suite->getName(), 'lead_lasso_app') !== false && $env == 'ci') {

    //         require_once dirname(__DIR__) . '/../../../../app/AppKernel.php';

    //         $kernel = new AppKernel($env, true);
    //         $kernel->boot();

    //         $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
    //         $application->setAutoExit(false);
    //         $options = array('command' => 'lead_lasso_app:database:create',"--fix" => true);
    //         $application->run(new \Symfony\Component\Console\Input\ArrayInput($options));

    //         $kernel->shutdown();
    //     } else {
    //     if (function_exists('xdebug_disable')) { xdebug_disable(); }
    // }

    }

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
    }

}
