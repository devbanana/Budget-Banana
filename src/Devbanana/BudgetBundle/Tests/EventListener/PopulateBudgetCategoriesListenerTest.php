<?php

namespace Devbanana\BudgetBundle\Test\EventListener;

require dirname(__FILE__) . '/../../../../../app/AppKernel.php';

use Devbanana\BudgetBundle\EventListener\PopulateBudgetCategoriesListener;
use Devbanana\BudgetBundle\Entity\Budget;
use Devbanana\BudgetBundle\Entity\Category;
use Devbanana\BudgetBundle\Entity\MasterCategory;

class PopulateBudgetCategoriesListenerTest extends \PHPUnit_Framework_TestCase
{

    protected static $kernel;
    protected static $container;

    public static function setUpBeforeClass()
    {
        self::$kernel = new \AppKernel('test', true);
        self::$kernel->boot();

        self::$container = self::$kernel->getContainer();
    }

    public function testPersistsEntities()
    {

        $uow = $this->getMockBuilder('\Doctrine\ORM\UnitOfWork')
            ->disableOriginalConstructor()
            ->getMock();

        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $args = $this->getMockBuilder('\Doctrine\ORM\Event\OnFlushEventArgs')
            ->disableOriginalConstructor()
            ->getMock();

        $args->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($em));

        $em->expects($this->once())
            ->method('getUnitOfWork')
            ->will($this->returnValue($uow));

        $ent1 = $this->getMockBuilder('\Devbanana\BudgetBundle\Entity\Budget')
            ->disableOriginalConstructor()
            ->getMock();

        $ent2 = $this->getMockBuilder('\stdClass')
            ->disableOriginalConstructor()
            ->getMock();

        $uow->expects($this->once())
            ->method('getScheduledEntityInsertions')
            ->will($this->returnValue(array($ent1, $ent2)));

        $repository = $this->getMockBuilder('\Devbanana\BudgetBundle\Entity\CategoryRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('DevbananaBudgetBundle:Category'))
            ->will($this->returnValue($repository));

        $cat1 = $this->getMockBuilder('\Devbanana\BudgetBundle\Entity\Category')
            ->disableOriginalConstructor()
            ->getMock();

        $repository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue(array($cat1)));

        $listener = $this->getMockBuilder('\Devbanana\BudgetBundle\EventListener\PopulateBudgetCategoriesListener')
            ->setMethods(array('getNewBudgetCategories'))
            ->getMock();

        $bc = $this->getMockBuilder('\Devbanana\BudgetBundle\Entity\BudgetCategories')
            ->disableOriginalConstructor()
            ->getMock();

        $listener->expects($this->once())
            ->method('getNewBudgetCategories')
            ->will($this->returnValue($bc));

        $bc->expects($this->once())
            ->method('setBudget')
            ->with($this->equalTo($ent1));

        $bc->expects($this->once())
            ->method('setCategory')
            ->with($this->equalTo($cat1));

        $em->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($bc));

        $metadata = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $em->expects($this->once())
            ->method('getClassMetadata')
            ->with($this->equalTo('Devbanana\BudgetBundle\Entity\BudgetCategories'))
            ->will($this->returnValue($metadata));

        $uow->expects($this->once())
            ->method('computeChangeSet')
            ->with($this->equalTo($metadata), $this->equalTo($bc));

        $listener->onFlush($args);

    }

    public function testCreatesCategories()
    {
        $em = self::$container->get('doctrine')->getManager();

        // Create a category
        $mc1 = new MasterCategory;
        $mc1->setName('Foo');

        $c1 = new Category;
        $c1->setName('Bar');
        $c1->setMasterCategory($mc1);

        $em->persist($c1);
        $em->persist($mc1);

        $em->flush();

        $budget = new Budget;
        $budget->setMonth(new \DateTime('2014-09-01'));

        $em->persist($budget);
        $em->flush();

        $bcs = $em->getRepository('DevbananaBudgetBundle:BudgetCategories')
            ->findByBudget($budget);

        $this->assertEquals(1, count($bcs));
        $this->assertEquals("Foo > Bar", "$bcs[0]");

        $em->remove($budget);
        if ($bcs) {
            $em->remove($bcs[0]);
        }

        $em->remove($c1);
        $em->remove($mc1);

        $em->flush();
    }

}
