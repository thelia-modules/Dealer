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

use Dealer\Dealer;
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
    const CONFIG_GOOGLE_API_KEY = "google_api_key";
    const GEOCODE_TIMEOUT = 3;

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            DealerEvents::DEALER_CREATE_AFTER => ["updateGeo", 128],
        ];
    }

    public function updateGeo(DealerEvent $event){
            $dealer = $event->getDealer();
            if (0.0 !== (float)$dealer->getLatitude() || 0.0 !== (float)$dealer->getLongitude()) {
                return;
            }

            if ($this->generateCoordinate($event)) {
                $dealer->save();
            }
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
        if ($apiKey = Dealer::getConfigValue(self::CONFIG_GOOGLE_API_KEY)) {
            $url .= "&key=" . urlencode($apiKey);
        }
        return $url;
    }

    /**
     * Get create|update event from Dealer to insert lat/lon param
     * @param DealerEvent $event
     * @return bool true if coordinates have been set on the dealer
     */
    public function generateCoordinate(DealerEvent $event)
    {
        if (!$url = $this->generateGoogleRequest($event)) {
            return false;
        }

        try {
            $context = stream_context_create([
                "http" => [
                    "method" => "GET",
                    "timeout" => self::GEOCODE_TIMEOUT,
                ],
            ]);

            $response = @file_get_contents($url, false, $context);
            if (!$response) {
                Tlog::getInstance()->addWarning("DEALER GOOGLE MAP : no response from geocoding service");

                return false;
            }

            $jsonEncoder = new JsonEncoder();
            $data = $jsonEncoder->decode($response, JsonEncoder::FORMAT);
            if (!isset($data["status"]) || strcmp($data["status"], "OK") != 0) {
                Tlog::getInstance()->addWarning("DEALER GOOGLE MAP : geocoding refused (" . ($data["status"] ?? "no status") . ")");

                return false;
            }

            $loc = $data["results"][0]["geometry"]["location"] ?? [];
            if (!isset($loc["lat"], $loc["lng"])) {
                Tlog::getInstance()->addWarning("DEALER GOOGLE MAP : no location in geocoding response");

                return false;
            }

            $event->getDealer()->setLatitude($loc["lat"]);
            $event->getDealer()->setLongitude($loc["lng"]);

            return true;
        } catch (\Throwable $ex) {
            Tlog::getInstance()->addWarning("DEALER GOOGLE MAP : " . $ex->getMessage());

            return false;
        }
    }


}