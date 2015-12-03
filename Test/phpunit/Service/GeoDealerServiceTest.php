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
use Dealer\Service\GeoDealerService;
use Propel\Runtime\Propel;
use Symfony\Component\EventDispatcher\EventDispatcher;

class GeoDealerServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var  GeoDealerService $geoService */
    protected $geoService;

    /** @var  Dealer $dealer */
    protected $dealer;

    public function setUp()
    {
        $this->geoService = new GeoDealerService();

        $this->mockEventDispatcher = $this->getMockBuilder(EventDispatcher::class)
            ->setMethods(['dispatch'])
            ->getMock();

        $dealerService = new DealerService();
        $dealerService->setDispatcher($this->mockEventDispatcher);

        $this->dealer = $dealerService->createFromArray($this->dataDealerRequire());

    }

    public static function setUpBeforeClass()
    {
        Propel::getConnection()->beginTransaction();
    }

    /**
     * @covers \Dealer\Service\GeoDealerService::updateFromArray()
     */
    public function testUpdateFromArrayWithoutId(){
        $dealer = $this->geoService->updateFromArray($this->dataMissingID());
        $this->assertNull($dealer);
    }

    /**
     * @covers \Dealer\Service\GeoDealerService::updateFromArray()
     */
    public function testUpdateFromArrayMissingElt(){
        $dealer = $this->geoService->updateFromArray($this->dataMissingElt());
        $this->assertEquals($dealer, DealerQuery::create()->findOneById($dealer->getId()));
    }

    /**
     * @covers \Dealer\Service\GeoDealerService::updateFromArray()
     */
    public function testUpdateFromArrayWithRequire(){
        $dealer = $this->geoService->updateFromArray($this->dataRequire());
        $this->assertEquals($dealer, DealerQuery::create()->findOneById($dealer->getId()));
    }

    public static function tearDownAfterClass()
    {
        Propel::getConnection()->rollBack();
    }



    protected function dataRequire()
    {
        return [
            'id' => $this->dealer->getId(),
            'latitude' => 5.1234,
            'longitude' => 0.23587
        ];
    }

    protected function dataMissingID()
    {
        return [
            'latitude' => 5.1234,
            'longitude' => 0.23587
        ];
    }

    protected function dataMissingElt()
    {
        return [
            'id' => $this->dealer->getId(),
            'longitude' => 0.23587
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
