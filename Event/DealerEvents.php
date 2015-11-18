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
/**
 * Class DealerEvents
 */
class DealerEvents
{
    const DEALER_CREATE = "dealer.dealer.create";
    const DEALER_CREATE_BEFORE = "dealer.dealer.create.before";
    const DEALER_CREATE_AFTER = "dealer.dealer.create.after";
    const DEALER_UPDATE = "dealer.dealer.update";
    const DEALER_UPDATE_BEFORE = "dealer.dealer.update.before";
    const DEALER_UPDATE_AFTER = "dealer.dealer.update.after";
    const DEALER_DELETE = "dealer.dealer.delete";
    const DEALER_DELETE_BEFORE = "dealer.dealer.delete.before";
    const DEALER_DELETE_AFTER = "dealer.dealer.delete.after";
}