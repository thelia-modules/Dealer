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

use Dealer\Loop\DealerLoop;
use Dealer\Model\Dealer;
use Dealer\Service\DealerService;
use Dealer\Test\PhpUnit\Base\AbstractPropelTest;
use Propel\Runtime\Util\PropelModelPager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DealerLoopTest extends AbstractPropelTest
{

    /** @var  DealerLoop $loop */
    protected $loop;

    /** @var  Dealer $dealer */
    protected $dealer;

    /**
     * Expected possible values for the order loop argument.
     * @var array
     */
    protected static $VALID_ORDER = [
        'id',
        'id-reverse',
        'date',
        'date-reverse'
    ];

    public function setUp()
    {
        $this->loop = new DealerLoop($this->container);

        $this->mockEventDispatcher = $this->getMockBuilder(EventDispatcher::class)
            ->setMethods(['dispatch'])
            ->getMock();

        $dealerService = new DealerService();
        $dealerService->setDispatcher($this->mockEventDispatcher);

        $this->dealer = $dealerService->createFromArray($this->dataDealerRequire(), "fr_FR");
    }

    /**
     * @inheritDoc
     */
    protected function buildContainer(ContainerBuilder $container)
    {
    }

    /**
     * @covers \Dealer\Loop\DealerLoop::initializeArgs()
     */
    public function testHasNoMandatoryArguments()
    {
        $this->loop->initializeArgs([]);
    }

    /**
     * @covers \Dealer\Loop\DealerLoop::initializeArgs()
     */
    public function testAcceptsAllOrderArguments()
    {
        foreach (static::$VALID_ORDER as $order) {
            $this->loop->initializeArgs(["order" => $order]);
        }
    }

    /**
     * @covers \Dealer\Loop\DealerLoop::initializeArgs()
     */
    public function testAcceptsAllArguments()
    {
        $this->loop->initializeArgs($this->getTestArg());
    }

    /**
     * @covers \Dealer\Loop\DealerLoop::buildModelCriteria()
     * @covers \Dealer\Loop\DealerLoop::exec()
     * @covers \Dealer\Loop\DealerLoop::parseResults()
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
        $this->assertEquals($this->dealer->getId(), $loopResultRow->get("ID"));
        $this->assertEquals($this->dealer->getCountryId(), $loopResultRow->get("COUNTRY_ID"));
        $this->assertEquals($this->dealer->getCity(), $loopResultRow->get("CITY"));
        $this->assertEquals($this->dealer->getAddress1(), $loopResultRow->get("ADDRESS1"));
        $this->assertEquals($this->dealer->getTitle(), $loopResultRow->get("TITLE"));
    }

    protected function getTestArg()
    {
        return [
            "id" => $this->dealer->getId(),
            "country_id" => $this->dealer->getCountryId(),
            "city" => $this->dealer->getCity()
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
