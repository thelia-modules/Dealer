<?php

namespace Dealer\Service;

use Dealer\Model\DealerQuery;
use Dealer\Model\DealerShedules;
use Dealer\Model\DealerShedulesQuery;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Flat dealer + weekly opening hours dump, with no consumer left in the module.
 *
 * @deprecated use the dealer loops for presentation, or PickupSlotService for the
 *             effective opening of a date (this class ignores the exceptional entries)
 */
class DealerUtils
{
    public function __construct(private readonly RequestStack $requestStack) {}

    public function getDealers(): array
    {
        $dealers = [];
        $request = $this->requestStack->getCurrentRequest();
        $locale = (null !== $request && $request->hasSession())
            ? $request->getSession()->getLang()->getLocale()
            : (\Thelia\Model\LangQuery::create()->findOneByByDefault(true)?->getLocale() ?? 'en_US');

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

    /**
     * The base weekly opening hours only, grouped by weekday: the exceptional entries
     * (dated, periodic or yearly openings and closures) are out of reach of this format.
     *
     * @deprecated use PickupSlotService::getAvailableSlots() to know when a dealer is open
     */
    public function getDealerSchedules($dealerId)
    {
        $scheduleModels = DealerShedulesQuery::create()
            ->filterByDealerId($dealerId)
            ->filterByException(false)
            ->filterByClosed(false)
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
