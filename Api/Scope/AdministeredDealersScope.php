<?php

declare(strict_types=1);

namespace Dealer\Api\Scope;

use Dealer\Dealer;
use Dealer\Model\DealerAdminQuery;
use Symfony\Bundle\SecurityBundle\Security;
use Thelia\Model\Admin;

/**
 * The back-office rule of the dealer list (Loop\Dealer::getAdminDealer()), applied to the API:
 * an administrator without a profile (superadmin), or whose profile is listed in the
 * `admin_profile_id` setting, reaches every store; any other one only the stores linked to them
 * in the "Users" tab of the store.
 */
final readonly class AdministeredDealersScope implements DealerScopeInterface
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function dealerIds(): ?array
    {
        $admin = $this->security->getUser();

        if (!$admin instanceof Admin) {
            return [];
        }

        if (null === $admin->getProfileId() || \in_array((int) $admin->getProfileId(), $this->profilesSeeingEveryStore(), true)) {
            return null;
        }

        return array_map(
            'intval',
            DealerAdminQuery::create()->filterByAdminId($admin->getId())->select(['DealerId'])->find()->getData(),
        );
    }

    /** @return list<int> */
    private function profilesSeeingEveryStore(): array
    {
        $configured = (string) Dealer::getConfigValue(Dealer::CONFIG_ALLOW_PROFILE_ID, '');

        return array_values(array_map('intval', array_filter(array_map('trim', explode(',', $configured)), 'strlen')));
    }
}
