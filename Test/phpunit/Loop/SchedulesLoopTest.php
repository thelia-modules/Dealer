<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/
/*************************************************************************************/

namespace Dealer\Test\Loop;


use Dealer\Loop\SchedulesLoop;
use Dealer\Model\Dealer;
use Dealer\Model\DealerShedules;
use Dealer\Service\DealerService;
use Dealer\Service\SchedulesService;
use Dealer\Test\PhpUnit\Base\AbstractPropelTest;
use Propel\Runtime\Util\PropelModelPager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SchedulesLoopTest extends AbstractPropelTest
{
    /** @var  SchedulesLoop $loop */
    protected $loop;

    /** @var  Dealer $dealer */
    protected $dealer;

    /** @var  DealerShedules $dealer */
    protected $defaultSchedules;

    /** @var  DealerShedules $dealer */
    protected $extraSchedules;

    /**
     * @inheritDoc
     */
    protected function buildContainer(ContainerBuilder $container)
    {

    }

    /**
     * Expected possible values for the order loop argument.
     * @var array
     */
    protected static $VALID_ORDER = [
        'id',
        'id-reverse',
        'day',
        'day-reverse',
    ];

    public function setUp()
    {
        $this->loop = new SchedulesLoop($this->container);

        $this->mockEventDispatcher = $this->getMockBuilder(EventDispatcher::class)
            ->setMethods(['dispatch'])
            ->getMock();

        /* Create Test Dealer */
        $dealerService = new DealerService();
        $dealerService->setDispatcher($this->mockEventDispatcher);
        $this->dealer = $dealerService->createFromArray($this->dataDealerRequire(), "fr_FR");

        /* Create Test Schedules */
        $schedulesService = new SchedulesService();
        $schedulesService->setDispatcher($this->mockEventDispatcher);

        $this->defaultSchedules = $schedulesService->createFromArray($this->dataDefaultRequire(), "fr_FR");
        $this->extraSchedules = $schedulesService->createFromArray($this->dataExtraRequire(), "fr_FR");
    }

    /**
     * @covers \Dealer\Loop\Dealer::initializeArgs()
     */
    public function testHasNoMandatoryArguments()
    {
        $this->loop->initializeArgs([]);
    }

    /**
     * @covers \Dealer\Loop\Dealer::initializeArgs()
     */
    public function testAcceptsAllOrderArguments()
    {
        foreach (static::$VALID_ORDER as $order) {
            $this->loop->initializeArgs(["order" => $order]);
        }
    }

    /**
     * @covers \Dealer\Loop\Dealer::initializeArgs()
     */
    public function testAcceptsAllDefaultArguments()
    {
        $this->loop->initializeArgs($this->getTestDefaultArg());
    }
    /**
     * @covers \Dealer\Loop\Dealer::initializeArgs()
     */
    public function testAcceptsAllExtraArguments()
    {
        $this->loop->initializeArgs($this->getTestExtraArg());
    }

    /**
     * @covers \Dealer\Loop\Dealer::buildModelCriteria()
     * @covers \Dealer\Loop\Dealer::exec()
     * @covers \Dealer\Loop\Dealer::parseResults()
     */
    public function testHasExpectedDefaultOutput()
    {
        $this->loop->initializeArgs($this->getTestDefaultArg());
        $loopResult = $this->loop->exec(
            new PropelModelPager($this->loop->buildModelCriteria())
        );

        $this->assertEquals(1, $loopResult->getCount());
        $loopResult->rewind();
        $loopResultRow = $loopResult->current();
        $this->assertEquals($this->defaultSchedules->getId(), $loopResultRow->get("ID"));
        $this->assertEquals($this->defaultSchedules->getDay(), $loopResultRow->get("DAY"));
        $this->assertEquals($this->defaultSchedules->getBegin(), $loopResultRow->get("BEGIN"));
        $this->assertEquals($this->defaultSchedules->getEnd(), $loopResultRow->get("END"));
    }

    /**
     * @covers \Dealer\Loop\Dealer::buildModelCriteria()
     * @covers \Dealer\Loop\Dealer::exec()
     * @covers \Dealer\Loop\Dealer::parseResults()
     */
    public function testHasExpectedExtraOutput()
    {
        $this->loop->initializeArgs($this->getTestExtraArg());
        $loopResult = $this->loop->exec(
            new PropelModelPager($this->loop->buildModelCriteria())
        );

        $this->assertEquals(1, $loopResult->getCount());
        $loopResult->rewind();
        $loopResultRow = $loopResult->current();
        $this->assertEquals($this->extraSchedules->getId(), $loopResultRow->get("ID"));
        $this->assertEquals($this->extraSchedules->getDay(), $loopResultRow->get("DAY"));
        $this->assertEquals($this->extraSchedules->getBegin(), $loopResultRow->get("BEGIN"));
        $this->assertEquals($this->extraSchedules->getEnd(), $loopResultRow->get("END"));
        $this->assertEquals($this->extraSchedules->getPeriodBegin(), $loopResultRow->get("PERIOD_BEGIN"));
        $this->assertEquals($this->extraSchedules->getPeriodEnd(), $loopResultRow->get("PERIOD_END"));
    }


    protected function getTestDefaultArg()
    {
        return [
            "id" => $this->defaultSchedules->getId(),
            "dealer_id" => $this->defaultSchedules->getDealerId(),
            "day" => $this->defaultSchedules->getDay(),
            "default_period" => true,
        ];
    }

    protected function getTestExtraArg()
    {
        return [
            "id" => $this->extraSchedules->getId(),
            "dealer_id" => $this->extraSchedules->getDealerId(),
            "day" => $this->extraSchedules->getDay(),
            "default_period" => false,
        ];
    }

    protected function dataDefaultRequire(){
        return [
            "day" => 0,
            "begin" => "8:00",
            "end" => "10:00",
            "dealer_id" => $this->dealer->getId()
        ];
    }

    protected function dataExtraRequire(){
        return [
            "day" => 0,
            "begin" => "8:00",
            "end" => "10:00",
            "period_begin" => "2015-11-11",
            "period_end" => "2016-11-16",
            "dealer_id" => $this->dealer->getId()
        ];
    }

    protected function dataDealerRequire()
    {
        return [
            "title" => "Openstudio",
            "address1" => "5 rue jean rochon",
            "zipcode" => "63000",
            "city" => "test",
            "country_id" => "64",
        ];
    }
}
