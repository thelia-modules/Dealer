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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\SecurityContext;
use Thelia\Core\Template\Parser\ParserResolver;
use Thelia\Core\Translation\Translator;

/**
 * Class AdminInterfaceHook
 */
class AdminInterfaceHook extends BaseHook
{
    public function __construct(
        private readonly SecurityContext $securityContext,
        ?EventDispatcherInterface $dispatcher = null,
        ?ParserResolver $parserResolver = null,
    ) {
        parent::__construct($dispatcher, $parserResolver);
    }

    public static function getSubscribedHooks(): array
    {
        return [
            'main.in-top-menu-items' => [
                ['type' => 'back', 'method' => 'onMainTopMenuTools'],
            ],
        ];
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
            $event->add($this->render("Dealer/menu-hook.html.twig", $event->getArguments()));
        }
    }
}
