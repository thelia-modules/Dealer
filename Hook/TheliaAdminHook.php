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

namespace Dealer\Hook;

use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

/**
 * Class TheliaAdminHook
 * @package Dealer\Hook
 */
class TheliaAdminHook extends BaseHook
{
    public function onContentModuleTab(HookRenderEvent $event)
    {
        $event->add($this->render("hook/content.html",$event->getArguments()));
    }

    public function onContentEditJs(HookRenderEvent $event){
        $event->add($this->render("script/dealer-content-js.html",$event->getArguments()));
    }
}