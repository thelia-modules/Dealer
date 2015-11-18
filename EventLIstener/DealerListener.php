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

namespace Dealer\EventListener;

use Dealer\Event\DealerEvent;
use Dealer\Event\DealerEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DealerListener
 * @package Dealer\EventListener
 */
class DealerListener implements EventSubscriberInterface
{

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            DealerEvents::DEALER_CREATE => ["createOrUpdate", 128],
            DealerEvents::DEALER_UPDATE => ["createOrUpdate", 128],
            DealerEvents::DEALER_DELETE => ["delete", 128],
        ];
    }

    public function crateOrUpdate(DealerEvent $event)
    {
        $event->getDealer()->save();
    }

    public function delete(DealerEvent $event)
    {
        $event->getDealer()->delete();
    }
}