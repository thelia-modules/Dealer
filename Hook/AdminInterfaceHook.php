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

use Dealer\Dealer;
use Symfony\Component\Routing\Router;
use Thelia\Core\Event\Hook\HookRenderBlockEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Tools\URL;
use Thelia\Core\Translation\Translator;

/**
 * Class AdminInterfaceHook
 */
class AdminInterfaceHook extends BaseHook
{
    protected $router;

    public function __construct(Router $router){
        $this->router = $router;
    }

    protected function transQuick($id, $locale, $parameters = [])
    {
        if ($this->translator === null) {
            $this->translator = Translator::getInstance();
        }

        return $this->trans($id, $parameters, Dealer::MESSAGE_DOMAIN, $locale);

    }

    public function onTopMenuTools(HookRenderBlockEvent $event)
    {

        $url = $this->router->generate("dealer.dealer_tab.list");
        $lang = $this->getSession()->getLang();
        $title = $this->transQuick("Dealer", $lang->getLocale());

        $event->add(
            [
                "id" => "dealer",
                "class" => "",
                "title" => $title,
                "url" => $url

            ]
        );
    }
}