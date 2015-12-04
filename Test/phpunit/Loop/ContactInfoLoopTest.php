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


use Dealer\Loop\ContactInfoLoop;
use Dealer\Model\DealerContactInfo;
use Dealer\Model\Dealer;
use Dealer\Model\DealerContact;
use Dealer\Service\ContactInfoService;
use Dealer\Service\ContactService;
use Dealer\Service\DealerService;
use Dealer\Test\PhpUnit\Base\AbstractPropelTest;
use Propel\Runtime\Util\PropelModelPager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ContactInfoLoopTest extends AbstractPropelTest
{
    /** @var  ContactInfoLoop $loop */
    protected $loop;

    /** @var  Dealer $dealer */
    protected $dealer;

    /** @var  DealerContact $dealer */
    protected $contact;

    /** @var  DealerContactInfo $dealer */
    protected $contactInfo;

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
        'value',
        'value-reverse',
    ];

    public function setUp()
    {
        $this->loop = new ContactInfoLoop($this->container);

        $this->mockEventDispatcher = $this->getMockBuilder(EventDispatcher::class)
            ->setMethods(['dispatch'])
            ->getMock();

        /* Create Test Dealer */
        $dealerService = new DealerService();
        $dealerService->setDispatcher($this->mockEventDispatcher);
        $this->dealer = $dealerService->createFromArray($this->dataDealerRequire(), "fr_FR");

        /* Create Test Contact */
        $contactService = new ContactService();
        $contactService->setDispatcher($this->mockEventDispatcher);
        $this->contact = $contactService->createFromArray($this->dataContactRequire(), "fr_FR");

        /* Create Test Contact Info */
        $contactInfoService = new ContactInfoService();
        $contactInfoService->setDispatcher($this->mockEventDispatcher);
        $this->contactInfo = $contactInfoService->createFromArray($this->dataContactInfoRequire(), "fr_FR");
    }

    /**
     * @covers \Dealer\Loop\ContactInfoLoop::initializeArgs()
     */
    public function testHasNoMandatoryArguments()
    {
        $this->loop->initializeArgs([]);
    }

    /**
     * @covers \Dealer\Loop\ContactInfoLoop::initializeArgs()
     */
    public function testAcceptsAllOrderArguments()
    {
        foreach (static::$VALID_ORDER as $order) {
            $this->loop->initializeArgs(["order" => $order]);
        }
    }

    /**
     * @covers \Dealer\Loop\ContactInfoLoop::initializeArgs()
     */
    public function testAcceptsAllArguments()
    {
        $this->loop->initializeArgs($this->getTestArg());
    }

    /**
     * @covers \Dealer\Loop\ContactInfoLoop::buildModelCriteria()
     * @covers \Dealer\Loop\ContactInfoLoop::exec()
     * @covers \Dealer\Loop\ContactInfoLoop::parseResults()
     */
    public function testHasExpectedOutput()
    {
        $this->loop->initializeArgs($this->getTestArg());
        $loopResult = $this->loop->exec(
            new PropelModelPager($this->loop->buildModelCriteria())
        );

        $this->assertEquals(1, $loopResult->getCount());
        $loopResult->rewind();
        $loopResultRow = $loopResult->current();
        $this->assertEquals($this->contactInfo->getId(), $loopResultRow->get("ID"));
        $this->assertEquals($this->contactInfo->getContactId(), $loopResultRow->get("CONTACT_ID"));
        $this->assertEquals($this->contactInfo->getContactType(), $loopResultRow->get("CONTACT_TYPE"));
        $this->assertEquals($this->contactInfo->getContactTypeId(), $loopResultRow->get("CONTACT_TYPE_ID"));
        $this->assertEquals($this->contactInfo->getValue(), $loopResultRow->get("VALUE"));
    }


    protected function getTestArg()
    {
        return [
            "id" => $this->contactInfo->getId(),
            "contact_id" => $this->contactInfo->getContactId(),
        ];
    }

    protected function dataContactInfoRequire()
    {
        return [
            "value" => "email",
            "type" => 0,
            "contact_id" => $this->contact->getId(),
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
