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
use Dealer\Model\DealerContact;
use Dealer\Model\DealerContactQuery;
use Dealer\Service\ContactService;
use Dealer\Service\DealerService;
use Propel\Runtime\Propel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ContactServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var ContactService $dealerService */
    protected $contactService;

    /** @var  EventDispatcherInterface $mockEventDispatcher */
    protected $mockEventDispatcher;

    /** @var Dealer $dealer */
    protected $dealer;

    public function setUp()
    {
        $this->contactService = new ContactService();
        $this->mockEventDispatcher = $this->getMockBuilder(EventDispatcher::class)
            ->setMethods(['dispatch'])
            ->getMock();
        $this->contactService->setDispatcher($this->mockEventDispatcher);

        $dealerService = new DealerService();
        $dealerService->setDispatcher($this->mockEventDispatcher);

        $this->dealer = $dealerService->createFromArray($this->dataDealerRequire());

    }

    public static function setUpBeforeClass()
    {
        Propel::getConnection()->beginTransaction();
    }

    /**
     * @covers \Dealer\Service\ContactService::createFromArray()
     * @expectedException \Exception
     */
    public function testCreateFromArrayWithEmptyArray()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(ContactService::EVENT_CREATE_BEFORE));
        $this->contactService->createFromArray([]);
    }

    /**
     * @covers \Dealer\Service\ContactService::createFromArray()
     * @expectedException \Exception
     */
    public function testCreateFromArrayWithErrorInfo()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(ContactService::EVENT_CREATE_BEFORE));
        $this->contactService->createFromArray($this->dataMissingRequire(), "fr_FR");
    }


    /**
     * @covers \Dealer\Service\ContactService::createFromArray()
     * @expectedException \Exception
     */
    public function testCreateFromArrayWithNull()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(ContactService::EVENT_CREATE_BEFORE));
        $this->contactService->createFromArray(null, "fr_FR");
    }

    /**
     * @covers \Dealer\Service\ContactService::createFromArray()
     */
    public function testCreateFromArrayWithBaseInfo()
    {
        $data = $this->dataRequire();

        $this->mockEventDispatcher->expects($this->exactly(2))->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(ContactService::EVENT_CREATE_BEFORE)],
                [$this->equalTo(ContactService::EVENT_CREATE_AFTER)]
            );

        $dealer = $this->contactService->createFromArray($data, "fr_FR");

        $this->assertEquals($dealer, DealerContactQuery::create()->findOneById($dealer->getId()));

        return $dealer;
    }

    /**
     * @covers  \Dealer\Service\ContactService::updateFromArray()
     * @param DealerContact $dealer
     * @depends testCreateFromArrayWithBaseInfo
     */
    public function testUpdateFromArrayAfterCreateFromArrayWithBaseInfo(DealerContact $dealer)
    {
        $data = $this->dataUpdate();
        $data['id'] = $dealer->getId();

        $this->mockEventDispatcher->expects($this->exactly(2))->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(ContactService::EVENT_UPDATE_BEFORE)],
                [$this->equalTo(ContactService::EVENT_UPDATE_AFTER)]
            );

        $dealer = $this->contactService->updateFromArray($data, "fr_FR");
        $this->assertEquals($dealer, DealerContactQuery::create()->findOneById($dealer->getId()));
    }

    /**
     * @covers \Dealer\Service\ContactService::updateFromArray()
     * @expectedException \Exception
     */
    public function testUpdateFromEmptyIdWithoutAllRequireField()
    {
        $data = $this->dataUpdate();

        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(ContactService::EVENT_UPDATE_BEFORE));

        $dealer = $this->contactService->updateFromArray($data, "fr_FR");
        $this->assertEquals($dealer, DealerContactQuery::create()->findOneById($dealer->getId()));
    }

    /**
     * @covers \Dealer\Service\ContactService::updateFromArray()
     */
    public function testUpdateFromEmptyIdWithAllRequireField()
    {
        $data = $this->dataRequire();

        $this->mockEventDispatcher->expects($this->exactly(2))->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(ContactService::EVENT_UPDATE_BEFORE)],
                [$this->equalTo(ContactService::EVENT_UPDATE_AFTER)]
            );

        $dealer = $this->contactService->updateFromArray($data, "fr_FR");
        $this->assertEquals($dealer, DealerContactQuery::create()->findOneById($dealer->getId()));
    }


    /**
     * @covers \Dealer\Service\ContactService::updateFromArray()
     * @expectedException \Exception
     */
    public function testUpdateFromEmptyIdNull()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(ContactService::EVENT_UPDATE_BEFORE));
        $this->contactService->updateFromArray(null);
    }

    /**
     * @covers \Dealer\Service\ContactService::updateFromArray()
     * @expectedException \Exception
     */
    public function testUpdateFromEmptyArray()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(ContactService::EVENT_UPDATE_BEFORE));

        $dealer = $this->contactService->updateFromArray([], "fr_FR");
        $this->assertEquals($dealer, DealerContactQuery::create()->findOneById($dealer->getId()));
    }


    /**
     * @covers  \Dealer\Service\ContactService::deleteFromId()
     * @param DealerContact $dealer
     * @depends testCreateFromArrayWithBaseInfo
     */
    public function testDeleteDealerAfterCreateFromArrayWithBaseInfo(DealerContact $dealer)
    {
        $this->mockEventDispatcher->expects($this->exactly(2))->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(ContactService::EVENT_DELETE_BEFORE)],
                [$this->equalTo(ContactService::EVENT_DELETE_AFTER)]
            );
        $id = $dealer->getId();
        $this->contactService->deleteFromId(['id' => $id]);

        $this->assertEmpty(DealerContactQuery::create()->findOneById($id));
    }

    /**
     * @covers \Dealer\Service\ContactService::deleteFromId()
     */
    public function testDeleteDealerWithoutPassId()
    {
        $this->mockEventDispatcher->expects($this->exactly(0))->method('dispatch');
        $this->contactService->deleteFromId([]);
    }

    /**
     * @covers \Dealer\Service\ContactService::deleteFromId()
     */
    public function testDeleteDealerWithIdNull()
    {
        $this->mockEventDispatcher->expects($this->exactly(0))->method('dispatch');
        $this->contactService->deleteFromId(null);
    }


    public static function tearDownAfterClass()
    {
        Propel::getConnection()->rollBack();
    }

    protected function dataRequire()
    {
        return [
            "label" => "Openstudio",
            "dealer_id" => $this->dealer->getId()
        ];
    }

    protected function dataUpdate()
    {
        return [
            "is_default" => "true",
        ];
    }


    protected function dataMissingRequire()
    {
        return [
            "is_default" => "false",
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
