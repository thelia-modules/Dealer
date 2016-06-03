<?php
/**
 * Created by PhpStorm.
 * User: apenalver
 * Date: 22/02/2016
 * Time: 16:59
 */

namespace Dealer\Hook;

use Dealer\Dealer;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Dealer\Model\DealerQuery;


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
        /*$event->add($this->render("includes/product-linked.html", $event->getArguments()));
        $event->add($this->render("modal/product-link.html", $event->getArguments()));*/
    }

    public function insertContentJs(HookRenderEvent $event)
    {
        $event->add($this->render("script/dealer-content-js.html", $event->getArguments()));
    }

    public function insertFolderJs(HookRenderEvent $event)
    {
        $event->add($this->render("script/dealer-folder-js.html", $event->getArguments()));
    }

    public function insertBrandJs(HookRenderEvent $event)
    {
        $event->add($this->render("script/dealer-brand-js.html", $event->getArguments()));
    }

    public function insertProductJs(HookRenderEvent $event)
    {
        //$event->add($this->render("script/dealer-product-js.html", $event->getArguments()));
    }
    
    public function insertGoogleMapsApiJs(HookRenderEvent $event)
    {
        $address = '';
        if($this->getRequest()->query->has('dealer_id')) {
            $dearler_id = $this->getRequest()->query->get('dealer_id');
            if($dearler_id != '') {
                $dealer = DealerQuery::create()->findOneById($dearler_id);
                $address = $dealer->getAddress1();
                if($dealer->getAddress2()) {
                    $address .= ', ' . $dealer->getAddress2();
                }
                if($dealer->getAddress3()) {
                    $address .= ', ' . $dealer->getAddress3();
                }
                $address .= ', ' . $dealer->getCity() . ', ' . $dealer->getZipcode() . ', ' . $dealer->getCountry()->getTitle();
            }
        }

        $event->add(
            $this->render(
                "script/dealer-geo-js.html",
                [
                    'address' => $address,
                    'googlemapsapi_key' => Dealer::getConfigValue('googlemapsapi_key')
                ]
            )
        );
    }
}