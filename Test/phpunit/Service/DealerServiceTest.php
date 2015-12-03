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
use Dealer\Model\DealerQuery;
use Dealer\Service\DealerService;
use Propel\Runtime\Propel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DealerServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var DealerService $dealerService */
    protected $dealerService;

    /** @var  EventDispatcherInterface $mockEventDispatcher */
    protected $mockEventDispatcher;

    public function setUp()
    {
        $this->dealerService = new DealerService();
        $this->mockEventDispatcher = $this->getMockBuilder(EventDispatcher::class)
            ->setMethods(['dispatch'])
            ->getMock();
        $this->dealerService->setDispatcher($this->mockEventDispatcher);
    }

    public static function setUpBeforeClass()
    {
        Propel::getConnection()->beginTransaction();
    }

    /**
     * @covers \Dealer\Service\DealerService::createFromArray()
     * @expectedException \Exception
     */
    public function testCreateFromArrayWithEmptyArray()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(DealerService::EVENT_CREATE_BEFORE));
        $this->dealerService->createFromArray([]);
    }

    /**
     * @covers \Dealer\Service\DealerService::createFromArray()
     * @expectedException \Exception
     */
    public function testCreateFromArrayWithErrorInfo()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(DealerService::EVENT_CREATE_BEFORE));
        $this->dealerService->createFromArray($this->dataMissingRequire(), "fr_FR");
    }


    /**
     * @covers \Dealer\Service\DealerService::createFromArray()
     * @expectedException \Exception
     */
    public function testCreateFromArrayWithNull()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(DealerService::EVENT_CREATE_BEFORE));
        $this->dealerService->createFromArray(null, "fr_FR");
    }

    /**
     * @covers \Dealer\Service\DealerService::createFromArray()
     */
    public function testCreateFromArrayWithBaseInfo()
    {
        $this->mockEventDispatcher->expects($this->exactly(2))->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(DealerService::EVENT_CREATE_BEFORE)],
                [$this->equalTo(DealerService::EVENT_CREATE_AFTER)]
            );
        $dealer = $this->dealerService->createFromArray($this->dataRequire(), "fr_FR");

        $this->assertEquals($dealer, DealerQuery::create()->findOneById($dealer->getId()));

        return $dealer;
    }

    /**
     * @param Dealer $dealer
     * @covers \Dealer\Service\DealerService::updateFromArray()
     * @depends testCreateFromArrayWithBaseInfo
     */
    public function testUpdateFromArrayAfterCreateFromArrayWithBaseInfo(Dealer $dealer)
    {
        $data = $this->dataUpdate();
        $data['id'] = $dealer->getId();

        $this->mockEventDispatcher->expects($this->exactly(2))->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(DealerService::EVENT_UPDATE_BEFORE)],
                [$this->equalTo(DealerService::EVENT_UPDATE_AFTER)]
            );

        $dealer = $this->dealerService->updateFromArray($data, "fr_FR");
        $this->assertEquals($dealer, DealerQuery::create()->findOneById($dealer->getId()));
    }

    /**
     * @covers \Dealer\Service\DealerService::updateFromArray()
     * @expectedException \Exception
     */
    public function testUpdateFromEmptyIdWithoutAllRequireField()
    {
        $data = $this->dataUpdate();

        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(DealerService::EVENT_UPDATE_BEFORE));

        $dealer = $this->dealerService->updateFromArray($data, "fr_FR");
        $this->assertEquals($dealer, DealerQuery::create()->findOneById($dealer->getId()));
    }

    /**
     * @covers \Dealer\Service\DealerService::updateFromArray()
     */
    public function testUpdateFromEmptyIdWithAllRequireField()
    {
        $data = $this->dataRequire();

        $this->mockEventDispatcher->expects($this->exactly(2))->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(DealerService::EVENT_UPDATE_BEFORE)],
                [$this->equalTo(DealerService::EVENT_UPDATE_AFTER)]
            );

        $dealer = $this->dealerService->updateFromArray($data, "fr_FR");
        $this->assertEquals($dealer, DealerQuery::create()->findOneById($dealer->getId()));
    }


    /**
     * @covers \Dealer\Service\DealerService::updateFromArray()
     * @expectedException \Exception
     */
    public function testUpdateFromEmptyIdNull()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(DealerService::EVENT_UPDATE_BEFORE));
        $this->dealerService->updateFromArray(null);
    }

    /**
     * @covers \Dealer\Service\DealerService::updateFromArray()
     * @expectedException \Exception
     */
    public function testUpdateFromEmptyArray()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(DealerService::EVENT_UPDATE_BEFORE));

        $dealer = $this->dealerService->updateFromArray([], "fr_FR");
        $this->assertEquals($dealer, DealerQuery::create()->findOneById($dealer->getId()));
    }

    /**
     * @param Dealer $dealer
     * @covers \Dealer\Service\DealerService::deleteFromId()
     * @depends testCreateFromArrayWithBaseInfo
     */
    public function testDeleteDealerAfterCreateFromArrayWithBaseInfo(Dealer $dealer)
    {
        $this->mockEventDispatcher->expects($this->exactly(2))->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(DealerService::EVENT_DELETE_BEFORE)],
                [$this->equalTo(DealerService::EVENT_DELETE_AFTER)]
            );
        $id = $dealer->getId();
        $this->dealerService->deleteFromId(['id' => $id]);

        $this->assertEmpty(DealerQuery::create()->findOneById($id));
    }

    /**
     * @covers \Dealer\Service\DealerService::deleteFromId()
     */
    public function testDeleteDealerWithoutPassId()
    {
        $this->mockEventDispatcher->expects($this->exactly(0))->method('dispatch');
        $this->dealerService->deleteFromId([]);
    }

    /**
     * @covers \Dealer\Service\DealerService::deleteFromId()
     */
    public function testDeleteDealerWithIdNull()
    {
        $this->mockEventDispatcher->expects($this->exactly(0))->method('dispatch');
        $this->dealerService->deleteFromId(null);
    }

    public static function tearDownAfterClass()
    {
        Propel::getConnection()->rollBack();
    }

    protected function dataRequire()
    {
        return [
            "title" => "Openstudio",
            "address1" => "5 rue jean rochon",
            "zipcode" => "63000",
            "city" => "test",
            "country_id" => "64",
        ];
    }

    protected function dataUpdate()
    {
        return [
            "city" => "testUp",
        ];
    }


    protected function dataMissingRequire()
    {
        return [
            "title" => "Openstudio",
        ];
    }


}
