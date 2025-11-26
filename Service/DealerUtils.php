<?php

namespace Dealer\Service;

use Dealer\Model\DealerQuery;
use Dealer\Model\DealerShedules;
use Dealer\Model\DealerShedulesQuery;
use Symfony\Component\HttpFoundation\RequestStack;

class DealerUtils
{
    public function __construct(private readonly RequestStack $requestStack) {}

    public function getDealers(): array
    {
        $dealers = [];
        $locale = $this->requestStack->getCurrentRequest()->getSession()->getLang()->getLocale();

        $dealerModels = DealerQuery::create()->find();
        foreach ($dealerModels as $dealer) {
            $dealer->setLocale($locale);
            $dealers[$dealer->getId()] = [
                'id' => $dealer->getId(),
                'title' => $dealer->getTitle(),
                'address1' => $dealer->getAddress1(),
                'address2' => $dealer->getAddress2(),
                'address3' => $dealer->getAddress3(),
                'zipcode' => $dealer->getZipcode(),
                'city' => $dealer->getCity(),
                'schedule' => $this->getDealerSchedules($dealer->getId()),
            ];
        }
        return $dealers;
    }

    public function getDealerSchedules($dealerId)
    {
        $scheduleModels = DealerShedulesQuery::create()
            ->filterByDealerId($dealerId)
            ->orderByDay()
            ->orderByBegin()
            ->find();

        $dealerSchedules = [];
        $dayHours = [];
        foreach ($scheduleModels as $schedule) {
            $day = $schedule->getDay();

            if (!isset($dayHours[$day])) {
                $dayHours[$day] = [];
            }
            $dayHours[$day][] = ['begin' => $schedule->getBegin('H:i'), 'end' => $schedule->getEnd('H:i') ];
            $dealerSchedules[$day] = [
                'day' => DealerShedules::DAYS[$day] ?? null,
                'hours' => $dayHours[$day]
            ];
        }
        return $dealerSchedules;
    }
}
