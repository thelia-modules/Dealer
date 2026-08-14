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
            'module.configuration' => [
                ['type' => 'back', 'method' => 'onModuleConfiguration'],
            ],
            'order-edit.delivery-module-bottom' => [
                ['type' => 'back', 'method' => 'onOrderDeliveryModuleBottom'],
            ],
        ];
    }

    public function onOrderDeliveryModuleBottom(HookRenderEvent $event): void
    {
        $orderId = (int) $event->getArgument('order_id');

        if ($orderId > 0) {
            $event->add($this->render('Dealer/hook/order-pickup-block.html.twig', ['order_id' => $orderId]));
        }
    }

    public function onModuleConfiguration(HookRenderEvent $event): void
    {
        $event->add($this->render('Dealer/module-configuration.html.twig'));
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
            $request = $this->getRequest();
            $dealerActive = $request !== null
                && str_starts_with($request->getPathInfo(), '/admin/module/Dealer');

            $event->add($this->render(
                "Dealer/menu-hook.html.twig",
                array_merge($event->getArguments(), ['dealer_active' => $dealerActive])
            ));
        }
    }
}
