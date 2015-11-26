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

namespace Dealer\Event;

use Dealer\Model\DealerShedules;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class DealerSchedulesEvent
 * @package Dealer\Event
 */
class DealerSchedulesEvent extends Event
{
    /**
     * @var DealerShedules
     */
    protected $dealer_schedules;

    /**
     * @return DealerShedules
     */
    public function getDealerSchedules()
    {
        return $this->dealer_schedules;
    }

    /**
     * @param DealerShedules $dealer_schedules
     */
    public function setDealerSchedules($dealer_schedules)
    {
        $this->dealer_schedules = $dealer_schedules;
    }


}