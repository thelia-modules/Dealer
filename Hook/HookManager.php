<?php
/*************************************************************************************/

namespace Dealer\Hook;

use Dealer\Dealer;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\ModuleConfig;
use Thelia\Model\ModuleConfigQuery;

class HookManager extends BaseHook
{

    public function onModuleConfigure(HookRenderEvent $event)
    {

        if (null !== $params = ModuleConfigQuery::create()->findByModuleId(Dealer::getModuleId())) {
            /** @var ModuleConfig $param */
            foreach ($params as $param) {
                $vars[ $param->getName() ] = $param->getValue();
            }
        }

        $event->add(
            $this->render('module-configuration.html', $vars)
        );
    }
}
