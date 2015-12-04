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

namespace Dealer\Test\Model;

use Dealer\Model\Dealer;
use Dealer\Model\DealerShedules;
use Dealer\Model\DealerShedulesQuery;
use Dealer\Service\DealerService;
use Dealer\Service\SchedulesService;
use Dealer\Test\PhpUnit\Base\AbstractPropelTest;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DealerShedulesQueryTest extends AbstractPropelTest
{

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

    public function setUp()
    {
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
     * @covers Dealer\Model\DealerShedulesQuery::filterByPeriodNotNull()
     */
    public function testFilterByPeriodNotNull()
    {
        $schedules = DealerShedulesQuery::create()->filterByPeriodNotNull()->find();

        $this->assertGreaterThanOrEqual(1, count($schedules));
        /** @var DealerShedules $schedule */
        foreach ($schedules as $schedule) {
            $this->assertNotNull($schedule->getPeriodBegin());
            $this->assertNotNull($schedule->getPeriodEnd());
        }
    }

    /**
     * @covers Dealer\Model\DealerShedulesQuery::filterByPeriodNull()
     */
    public function testFilterByPeriodNull()
    {
        $schedules = DealerShedulesQuery::create()->filterByPeriodNull()->find();

        $this->assertGreaterThanOrEqual(1, count($schedules));
        /** @var DealerShedules $schedule */
        foreach ($schedules as $schedule) {
            $this->assertNull($schedule->getPeriodBegin());
            $this->assertNull($schedule->getPeriodEnd());
        }
    }


    protected function dataDefaultRequire()
    {
        return [
            "day" => 0,
            "begin" => "8:00",
            "end" => "10:00",
            "dealer_id" => $this->dealer->getId()
        ];
    }

    protected function dataExtraRequire()
    {
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
