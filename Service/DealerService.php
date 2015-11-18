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

namespace Dealer\Service;

use Dealer\Event\DealerEvent;
use Dealer\Event\DealerEvents;
use Dealer\Form\DealerForm;
use Dealer\Service\Base\BaseService;

/**
 * Class DealerService
 * @package Dealer\Service
 */
class DealerService extends BaseService
{
    const EVENT_CREATE = DealerEvents::DEALER_CREATE;
    const EVENT_CREATE_BEFORE = DealerEvents::DEALER_CREATE_AFTER;
    const EVENT_CREATE_AFTER = DealerEvents::DEALER_CREATE_BEFORE;

    protected function createProcess(DealerEvent $event)
    {
        $event->getDealer()->save();
    }

    protected function updateProcess(DealerEvent $event)
    {
        $event->getDealer()->save();
    }

}