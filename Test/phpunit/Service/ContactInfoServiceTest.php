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
use Dealer\Model\DealerContactInfo;
use Dealer\Model\DealerContactInfoQuery;
use Dealer\Service\ContactInfoService;
use Dealer\Service\ContactService;
use Dealer\Service\DealerService;
use Propel\Runtime\Propel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ContactInfoServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var ContactInfoService $dealerService */
    protected $contactInfoService;

    /** @var  EventDispatcherInterface $mockEventDispatcher */
    protected $mockEventDispatcher;

    /** @var Dealer $dealer */
    protected $dealer;

    /** @var DealerContact $contact */
    protected $contact;

    public function setUp()
    {
        $this->contactInfoService = new ContactInfoService();
        $this->mockEventDispatcher = $this->getMockBuilder(EventDispatcher::class)
            ->setMethods(['dispatch'])
            ->getMock();
        $this->contactInfoService->setDispatcher($this->mockEventDispatcher);

        /* Create Test Dealer */
        $dealerService = new DealerService();
        $dealerService->setDispatcher($this->mockEventDispatcher);
        $this->dealer = $dealerService->createFromArray($this->dataDealerRequire());

        /* Create Test Contact */
        $contactService = new ContactService();
        $contactService->setDispatcher($this->mockEventDispatcher);
        $this->contact = $contactService->createFromArray($this->dataContactRequire());

    }

    public static function setUpBeforeClass()
    {
        Propel::getConnection()->beginTransaction();
    }

    /**
     * @covers \Dealer\Service\ContactInfoService::createFromArray()
     * @expectedException \Exception
     */
    public function testCreateFromArrayWithEmptyArray()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(ContactInfoService::EVENT_CREATE_BEFORE));
        $this->contactInfoService->createFromArray([]);
    }

    /**
     * @covers \Dealer\Service\ContactInfoService::createFromArray()
     * @expectedException \Exception
     */
    public function testCreateFromArrayWithErrorInfo()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(ContactInfoService::EVENT_CREATE_BEFORE));
        $this->contactInfoService->createFromArray($this->dataMissingRequire(), "fr_FR");
    }


    /**
     * @covers \Dealer\Service\ContactInfoService::createFromArray()
     * @expectedException \Exception
     */
    public function testCreateFromArrayWithNull()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(ContactInfoService::EVENT_CREATE_BEFORE));
        $this->contactInfoService->createFromArray(null, "fr_FR");
    }

    /**
     * @covers \Dealer\Service\ContactInfoService::createFromArray()
     */
    public function testCreateFromArrayWithBaseInfo()
    {
        $data = $this->dataRequire();

        $this->mockEventDispatcher->expects($this->exactly(2))->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(ContactInfoService::EVENT_CREATE_BEFORE)],
                [$this->equalTo(ContactInfoService::EVENT_CREATE_AFTER)]
            );

        $dealer = $this->contactInfoService->createFromArray($data, "fr_FR");

        $this->assertEquals($dealer, DealerContactInfoQuery::create()->findOneById($dealer->getId()));

        return $dealer;
    }

    /**
     * @param DealerContact $dealer
     * @covers  \Dealer\Service\ContactInfoService::updateFromArray()
     * @depends testCreateFromArrayWithBaseInfo
     */
    public function testUpdateFromArrayAfterCreateFromArrayWithBaseInfo(DealerContactInfo $dealer)
    {
        $data = $this->dataUpdate();
        $data['id'] = $dealer->getId();

        $this->mockEventDispatcher->expects($this->exactly(2))->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(ContactInfoService::EVENT_UPDATE_BEFORE)],
                [$this->equalTo(ContactInfoService::EVENT_UPDATE_AFTER)]
            );

        $dealer = $this->contactInfoService->updateFromArray($data, "fr_FR");
        $this->assertEquals($dealer, DealerContactInfoQuery::create()->findOneById($dealer->getId()));
    }

    /**
     * @covers \Dealer\Service\ContactInfoService::updateFromArray()
     * @expectedException \Exception
     */
    public function testUpdateFromEmptyIdWithoutAllRequireField()
    {
        $data = $this->dataUpdate();

        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(ContactInfoService::EVENT_UPDATE_BEFORE));

        $dealer = $this->contactInfoService->updateFromArray($data, "fr_FR");
        $this->assertEquals($dealer, DealerContactInfoQuery::create()->findOneById($dealer->getId()));
    }

    /**
     * @covers \Dealer\Service\ContactInfoService::updateFromArray()
     */
    public function testUpdateFromEmptyIdWithAllRequireField()
    {
        $data = $this->dataRequire();

        $this->mockEventDispatcher->expects($this->exactly(2))->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(ContactInfoService::EVENT_UPDATE_BEFORE)],
                [$this->equalTo(ContactInfoService::EVENT_UPDATE_AFTER)]
            );

        $dealer = $this->contactInfoService->updateFromArray($data, "fr_FR");
        $this->assertEquals($dealer, DealerContactInfoQuery::create()->findOneById($dealer->getId()));
    }


    /**
     * @covers \Dealer\Service\ContactInfoService::updateFromArray()
     * @expectedException \Exception
     */
    public function testUpdateFromEmptyIdNull()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(ContactInfoService::EVENT_UPDATE_BEFORE));
        $this->contactInfoService->updateFromArray(null);
    }

    /**
     * @covers \Dealer\Service\ContactInfoService::updateFromArray()
     * @expectedException \Exception
     */
    public function testUpdateFromEmptyArray()
    {
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo(ContactInfoService::EVENT_UPDATE_BEFORE));

        $dealer = $this->contactInfoService->updateFromArray([], "fr_FR");
        $this->assertEquals($dealer, DealerContactInfoQuery::create()->findOneById($dealer->getId()));
    }


    /**
     * @param DealerContact $dealer
     * @covers  \Dealer\Service\ContactInfoService::deleteFromId()
     * @depends testCreateFromArrayWithBaseInfo
     */
    public function testDeleteDealerAfterCreateFromArrayWithBaseInfo(DealerContactInfo $dealer)
    {
        $this->mockEventDispatcher->expects($this->exactly(2))->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(ContactInfoService::EVENT_DELETE_BEFORE)],
                [$this->equalTo(ContactInfoService::EVENT_DELETE_AFTER)]
            );
        $id = $dealer->getId();
        $this->contactInfoService->deleteFromId(['id' => $id]);

        $this->assertEmpty(DealerContactInfoQuery::create()->findOneById($id));
    }

    /**
     * @covers \Dealer\Service\ContactInfoService::deleteFromId()
     */
    public function testDeleteDealerWithoutPassId()
    {
        $this->mockEventDispatcher->expects($this->exactly(0))->method('dispatch');
        $this->contactInfoService->deleteFromId([]);
    }

    /**
     * @covers \Dealer\Service\ContactInfoService::deleteFromId()
     */
    public function testDeleteDealerWithIdNull()
    {
        $this->mockEventDispatcher->expects($this->exactly(0))->method('dispatch');
        $this->contactInfoService->deleteFromId(null);
    }

    public static function tearDownAfterClass()
    {
        Propel::getConnection()->rollBack();
    }

    protected function dataRequire()
    {
        return [
            "value" => "email",
            "type" => 0,
            "contact_id" => $this->contact->getId(),
        ];
    }

    protected function dataUpdate()
    {
        return [
            "value" => "tel",
            "type" => 1,
        ];
    }


    protected function dataMissingRequire()
    {
        return [
            "value" => "tel",
            "type" => 1,
        ];
    }

    protected function dataContactRequire()
    {
        return [
            "label" => "Openstudio",
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
