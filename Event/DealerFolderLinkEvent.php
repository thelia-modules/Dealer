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

use Dealer\Model\DealerFolder;
use Thelia\Core\Event\ActionEvent;

/**
 * Class DealerFolderLinkEvent
 * @package Dealer\Event
 */
class DealerFolderLinkEvent extends ActionEvent
{
    /**
     * @var DealerFolder $dealerFolderLink
     */
    protected $dealerFolderLink;

    /**
     * @return DealerFolder
     */
    public function getDealerFolderLink()
    {
        return $this->dealerFolderLink;
    }

    /**
     * @param DealerFolder $dealerFolderLink
     */
    public function setDealerFolderLink($dealerFolderLink)
    {
        $this->dealerFolderLink = $dealerFolderLink;
    }
}
