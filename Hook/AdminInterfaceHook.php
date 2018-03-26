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
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\SecurityContext;
use Thelia\Core\Translation\Translator;

/**
 * Class AdminInterfaceHook
 */
class AdminInterfaceHook extends BaseHook
{
    protected $router;

    protected $securityContext;

    public function __construct(Router $router, SecurityContext $securityContext)
    {
        $this->router = $router;
        $this->securityContext = $securityContext;
    }

    protected function transQuick($id, $locale, $parameters = [])
    {
        if ($this->translator === null) {
            $this->translator = Translator::getInstance();
        }

        return $this->trans($id, $parameters, Dealer::MESSAGE_DOMAIN, $locale);
    }

    public function onMainTopMenuTools(HookRenderEvent $event)
    {
        $isGranted = $this->securityContext->isGranted(
            ["ADMIN"],
            [],
            [Dealer::getModuleCode()],
            [AccessManager::VIEW]
        );

        if ($isGranted) {
            $event->add($this->render("menu-hook.html", $event->getArguments()));
        }
    }
}
