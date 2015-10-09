<?php

class TestListener implements \PHPUnit_Framework_TestListener
{
    public function startTest(PHPUnit_Framework_Test $test)
    {
    }

    public function endTest(PHPUnit_Framework_Test $test, $length)
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

    public function addRiskyTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
    }

    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        if (strpos($suite->getName(), 'KunstmaanTranslationBundle') === false) {
            return true;
        }

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
    }

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
    }

}
