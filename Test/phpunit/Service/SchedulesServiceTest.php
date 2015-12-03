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

namespace Dealer\Test\Service;


use Dealer\Model\Dealer;
use Dealer\Model\DealerShedules;
use Dealer\Model\DealerShedulesQuery;
use Dealer\Service\DealerService;
use Dealer\Service\SchedulesService;
use Propel\Runtime\Propel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SchedulesServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var SchedulesService $schedulesService */
    protected $schedulesService;

    /** @var  EventDispatcherInterface $mockEventDispatcher */
    protected $mockEventDispatcher;

    /** @var Dealer $dealer */
    protected $dealer;

    public function setUp()
    {
        $this->schedulesService = new SchedulesService();
        $this->mockEventDispatcher = $this->getMockBuilder(EventDispatcher::class)
            ->setMethods(['dispatch'])
            ->getMock();
        $this->schedulesService->setDispatcher($this->mockEventDispatcher);

        $dealerService = new DealerService();
        $dealerService->setDispatcher($this->mockEventDispatcher);

        $this->dealer = $dealerService->createFromArray($this->dataDealerRequire());

    }

    public static function setUpBeforeClass()
    {
        Propel::getConnection()->beginTransaction();
    }

    /**
     * @covers \Dealer\Service\SchedulesService::createFromArray()
     * @expectedException \Exception
     */
    public function testCreateFromArrayWithEmptyArray()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(SchedulesService::EVENT_CREATE_BEFORE));
        $this->schedulesService->createFromArray([]);
    }

    /**
     * @covers \Dealer\Service\SchedulesService::createFromArray()
     * @expectedException \Exception
     */
    public function testCreateFromArrayWithErrorInfo()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(SchedulesService::EVENT_CREATE_BEFORE));
        $this->schedulesService->createFromArray($this->dataMissingRequire(), "fr_FR");
    }


    /**
     * @covers \Dealer\Service\SchedulesService::createFromArray()
     * @expectedException \Exception
     */
    public function testCreateFromArrayWithNull()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(SchedulesService::EVENT_CREATE_BEFORE));
        $this->schedulesService->createFromArray(null, "fr_FR");
    }

    /**
     * @covers \Dealer\Service\SchedulesService::createFromArray()
     * @return DealerShedules
     */
    public function testCreateFromArrayWithDefaultBaseInfo()
    {
        $data = $this->dataRequire();

        $this->mockEventDispatcher->expects($this->exactly(2))->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(SchedulesService::EVENT_CREATE_BEFORE)],
                [$this->equalTo(SchedulesService::EVENT_CREATE_AFTER)]
            );

        $dealer = $this->schedulesService->createFromArray($data, "fr_FR");

        $this->assertEquals($dealer, DealerShedulesQuery::create()->findOneById($dealer->getId()));

        return $dealer;
    }

    /**
     * @covers \Dealer\Service\SchedulesService::createFromArray()
     * @return DealerShedules
     */
    public function testCreateFromArrayWithExtraBaseInfo()
    {
        $data = $this->dataExtraRequire();

        $this->mockEventDispatcher->expects($this->exactly(2))->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(SchedulesService::EVENT_CREATE_BEFORE)],
                [$this->equalTo(SchedulesService::EVENT_CREATE_AFTER)]
            );

        $dealer = $this->schedulesService->createFromArray($data, "fr_FR");

        $this->assertEquals($dealer, DealerShedulesQuery::create()->findOneById($dealer->getId()));

        return $dealer;
    }

    /**
     * @covers  \Dealer\Service\SchedulesService::updateFromArray()
     * @param DealerShedules $dealer
     * @depends testCreateFromArrayWithDefaultBaseInfo
     */
    public function testUpdateFromArrayAfterCreateFromArrayWithDefaultBaseInfo(DealerShedules $dealer)
    {
        $data = $this->dataUpdate();
        $data['id'] = $dealer->getId();

        $this->mockEventDispatcher->expects($this->exactly(2))->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(SchedulesService::EVENT_UPDATE_BEFORE)],
                [$this->equalTo(SchedulesService::EVENT_UPDATE_AFTER)]
            );

        $dealer = $this->schedulesService->updateFromArray($data, "fr_FR");
        $this->assertEquals($dealer, DealerShedulesQuery::create()->findOneById($dealer->getId()));
    }

    /**
     * @covers  \Dealer\Service\SchedulesService::updateFromArray()
     * @param DealerShedules $dealer
     * @depends testCreateFromArrayWithExtraBaseInfo
     */
    public function testUpdateFromArrayAfterCreateFromArrayWithExtraBaseInfo(DealerShedules $dealer)
    {
        $data = $this->dataExtraUpdate();
        $data['id'] = $dealer->getId();

        $this->mockEventDispatcher->expects($this->exactly(2))->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(SchedulesService::EVENT_UPDATE_BEFORE)],
                [$this->equalTo(SchedulesService::EVENT_UPDATE_AFTER)]
            );

        $dealer = $this->schedulesService->updateFromArray($data, "fr_FR");
        $this->assertEquals($dealer, DealerShedulesQuery::create()->findOneById($dealer->getId()));
    }

    /**
     * @covers \Dealer\Service\SchedulesService::updateFromArray()
     * @expectedException \Exception
     */
    public function testUpdateFromEmptyIdWithoutAllRequireField()
    {
        $data = $this->dataUpdate();

        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(SchedulesService::EVENT_UPDATE_BEFORE));

        $dealer = $this->schedulesService->updateFromArray($data, "fr_FR");
        $this->assertEquals($dealer, DealerShedulesQuery::create()->findOneById($dealer->getId()));
    }

    /**
     * @covers \Dealer\Service\SchedulesService::updateFromArray()
     */
    public function testUpdateFromEmptyIdWithAllRequireField()
    {
        $data = $this->dataRequire();

        $this->mockEventDispatcher->expects($this->exactly(2))->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(SchedulesService::EVENT_UPDATE_BEFORE)],
                [$this->equalTo(SchedulesService::EVENT_UPDATE_AFTER)]
            );

        $dealer = $this->schedulesService->updateFromArray($data, "fr_FR");
        $this->assertEquals($dealer, DealerShedulesQuery::create()->findOneById($dealer->getId()));
    }


    /**
     * @covers \Dealer\Service\SchedulesService::updateFromArray()
     * @expectedException \Exception
     */
    public function testUpdateFromEmptyIdNull()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(SchedulesService::EVENT_UPDATE_BEFORE));
        $this->schedulesService->updateFromArray(null);
    }

    /**
     * @covers \Dealer\Service\SchedulesService::updateFromArray()
     * @expectedException \Exception
     */
    public function testUpdateFromEmptyArray()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(SchedulesService::EVENT_UPDATE_BEFORE));

        $dealer = $this->schedulesService->updateFromArray([], "fr_FR");
        $this->assertEquals($dealer, DealerShedulesQuery::create()->findOneById($dealer->getId()));
    }


    /**
     * @covers  \Dealer\Service\SchedulesService::deleteFromId()
     * @param DealerShedules $dealer
     * @depends testCreateFromArrayWithDefaultBaseInfo
     */
    public function testDeleteDealerAfterCreateFromArrayWithBaseInfo(DealerShedules $dealer)
    {
        $this->mockEventDispatcher->expects($this->exactly(2))->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(SchedulesService::EVENT_DELETE_BEFORE)],
                [$this->equalTo(SchedulesService::EVENT_DELETE_AFTER)]
            );
        $id = $dealer->getId();
        $this->schedulesService->deleteFromId(['id' => $id]);

        $this->assertEmpty(DealerShedulesQuery::create()->findOneById($id));
    }

    /**
     * @covers \Dealer\Service\SchedulesService::deleteFromId()
     */
    public function testDeleteDealerWithoutPassId()
    {
        $this->mockEventDispatcher->expects($this->exactly(0))->method('dispatch');
        $this->schedulesService->deleteFromId([]);
    }

    /**
     * @covers \Dealer\Service\SchedulesService::deleteFromId()
     */
    public function testDeleteDealerWithIdNull()
    {
        $this->mockEventDispatcher->expects($this->exactly(0))->method('dispatch');
        $this->schedulesService->deleteFromId(null);
    }


    public static function tearDownAfterClass()
    {
        Propel::getConnection()->rollBack();
    }

    protected function dataRequire()
    {
        return [
            "day" => 0,
            "begin" => "8:30",
            "end" => "15:30",
            "dealer_id" => $this->dealer->getId()
        ];
    }

    protected function dataExtraRequire()
    {
        return [
            "day" => 0,
            "begin" => "8:30",
            "end" => "15:30",
            "period_begin" => "2015-03-12",
            "period_end" => "2015-09-12",
            "dealer_id" => $this->dealer->getId()
        ];
    }

    protected function dataUpdate()
    {
        return [
            "day" => 1,
        ];
    }

    protected function dataExtraUpdate()
    {
        return [
            "period_end" => "2015-12-12",
        ];
    }


    protected function dataMissingRequire()
    {
        return [
            "day" => 3,
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
