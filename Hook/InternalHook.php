<?php
/**
 * Created by PhpStorm.
 * User: apenalver
 * Date: 22/02/2016
 * Time: 16:59
 */

namespace Dealer\Hook;

use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;


/**
 * Class InternalHook
 * @package Dealer\Hook
 */
class InternalHook extends BaseHook
{
    public function insertContent(HookRenderEvent $event)
    {
        $event->add($this->render("includes/content-linked.html", $event->getArguments()));
        $event->add($this->render("modal/content-link.html", $event->getArguments()));
    }

    public function insertFolder(HookRenderEvent $event)
    {
        $event->add($this->render("includes/folder-linked.html", $event->getArguments()));
        $event->add($this->render("modal/folder-link.html", $event->getArguments()));
    }

    public function insertBrand(HookRenderEvent $event)
    {
        $event->add($this->render("includes/brand-linked.html", $event->getArguments()));
        $event->add($this->render("modal/brand-link.html", $event->getArguments()));
    }

    public function insertProduct(HookRenderEvent $event)
    {
        $event->add($this->render("includes/product-linked.html", $event->getArguments()));
        $event->add($this->render("modal/product-link.html", $event->getArguments()));
    }
}