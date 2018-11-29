<?php

namespace Kunstmaan\FormBundle\Tests\AdminList;

use Codeception\Stub;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\FormBundle\AdminList\FormSubmissionExportListConfigurator;
use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\BooleanFormSubmissionField;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Symfony\Component\Translation\Translator;

class FakeFormSubmission extends FormSubmission
{
    public function setFields(ArrayCollection $fields)
    {
        $this->fields = $fields;
    }
}

/**
 * This test tests the FormPageAdminListConfigurator
 */
class FormSubmissionExportListConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormSubmissionExportListConfigurator
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $em = $this->getMockedEntityManager();
        $node = new Node();
        $node->setId(666);
        $nt = new NodeTranslation();
        $nt->setNode($node);
        $translator = new Translator('nl');
        $this->object = new FormSubmissionExportListConfigurator($em, $nt, $translator);
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getMockedEntityManager()
    {
        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $field = new BooleanFormSubmissionField();
        $field->setFieldName('check');
        $field->setValue(true);

        $sub = new FakeFormSubmission();
        $sub->setFields(new ArrayCollection([
            $field,
        ]));

        $submissions = [
            [$sub],
            [new FormSubmission()],
            [new FormSubmission()],
        ];
        $query = $this->getMockBuilder('Doctrine\ORM\AbstractQuery')
            ->disableOriginalConstructor()
            ->getMock();

        $query->expects($this->any())
            ->method('iterate')
            ->willReturn($submissions);

        $methods = [
            'select', 'from', 'innerJoin', 'andWhere', 'setParameter', 'addOrderBy',
        ];
        foreach ($methods as $method) {
            $queryBuilder->expects($this->any())
                ->method($method)
                ->willReturn($queryBuilder);
        }

        $queryBuilder->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);

        $configuration = Stub::make(Configuration::class, [
            'getQuoteStrategy' => null,
        ]);
        $repository = Stub::make(EntityRepository::class, [
            'find' => null,
            'findBy' => null,
            'findOneBy' => null,
        ]);
        /** @var \Doctrine\ORM\EntityManager $emMock */
        $emMock = Stub::make(EntityManager::class, [
            'getRepository' => $repository,
            'getClassMetaData' => (object) ['name' => 'aClass'],
            'getConfiguration' => $configuration,
            'clear' => null,
            'createQueryBuilder' => $queryBuilder,
            'persist' => null,
            'flush' => null,
        ]);

        return $emMock;
    }

    public function testGetStringValue()
    {
        $this->assertNull($this->object->buildFilters());
        $this->assertEquals('', $this->object->getStringValue([], 'fail'));
        $this->assertEquals('pass', $this->object->getStringValue(['test' => 'pass'], 'test'));
    }

    public function testBuildExportFields()
    {
        $this->object->buildExportFields();
        $this->assertCount(3, $this->object->getExportFields());
        $this->object->addExportField('abc', 'def');
        $this->assertCount(4, $this->object->getExportFields());
    }

    public function testBuildIterator()
    {
        $this->object->buildIterator();
        $filters = $this->object->getIterator();
        $this->assertInstanceOf(\ArrayIterator::class, $filters);
        $first = $filters->current();
        $this->assertCount(1, $first);
        $first = $first[0];
        $this->assertArrayHasKey('check', $first);
        $this->assertEquals('true', $first['check']);
    }
}
