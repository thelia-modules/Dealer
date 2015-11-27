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

namespace Dealer\EventListener;

use Dealer\Event\DealerEvent;
use Dealer\Event\DealerEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Thelia\Log\Tlog;

/**
 * Class DealerListener
 * @package Dealer\EventListener
 */
class DealerListener implements EventSubscriberInterface
{

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            DealerEvents::DEALER_CREATE_AFTER => ["updateGeo", 128],
        ];
    }

    public function updateGeo(DealerEvent $event){
            $this->generateCoordinate($event);
            $event->getDealer()->save();
    }

    /**
     * Generate Address for Google API request
     * @param DealerEvent $event
     * @return string
     */
    protected function generateAddress(DealerEvent $event)
    {
        $dealer = $event->getDealer();
        $address = $dealer->getAddress1();
        if ($dealer->getAddress2()) {
            $address .= " " . $dealer->getAddress2();
        }
        $address .= " " . $dealer->getZipcode() . " " . $dealer->getCity();
        $address = str_replace(" ", "+", $address);
        return $address;
    }

    /**
     * Generate URL for Google API request
     * @param DealerEvent $event
     * @return string
     */
    protected function generateGoogleRequest(DealerEvent $event)
    {
        $url = "https://maps.googleapis.com/maps/api/geocode/json?";
        $url .= "address=" . $this->generateAddress($event);
        return $url;
    }

    /**
     * Get create|update event from Dealer to insert lat/lon param
     * @param DealerEvent $event
     */
    public function generateCoordinate(DealerEvent $event)
    {
        if ($url = $this->generateGoogleRequest($event)) {
            try {
                $response = file_get_contents($url);
                if ($response) {
                    $jsonEncoder = new JsonEncoder();
                    $data = $jsonEncoder->decode($response, JsonEncoder::FORMAT);
                    if(isset($data["status"]) && strcmp($data["status"],"OK") == 0){
                        $loc = $data["results"][0]["geometry"]["location"];
                        $event->getDealer()->setLatitude($loc["lat"]);
                        $event->getDealer()->setLongitude($loc["lng"]);
                    }
                }
            } catch (\ErrorException $ex) {
                Tlog::getInstance()->error("DEALER GOOGLE MAP : " . $ex->getMessage());
            }
        }
    }


}