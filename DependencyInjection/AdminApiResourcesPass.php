<?php

declare(strict_types=1);

namespace Dealer\DependencyInjection;

use Dealer\Dealer as DealerModule;
use Dealer\Api\Resource\Dealer;
use Dealer\Api\Resource\DealerContact;
use Dealer\Api\Resource\DealerContactInfo;
use Dealer\Api\Resource\DealerPickupConfig;
use Dealer\Api\Resource\DealerSchedule;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Gives each resource the back-office permission of the tab it mirrors. Without an entry in
 * `thelia.api.admin_resources`, the core AdminApiPermissionListener refuses the endpoint to
 * every administrator but a superadmin.
 *
 * Merged rather than set: the parameter is one array shared by every module publishing admin
 * resources. The pickup settings tab asks the back office for the generic module right scoped
 * to Dealer, which the API cannot scope to a module: they follow the store right instead.
 */
final readonly class AdminApiResourcesPass implements CompilerPassInterface
{
    private const PARAMETER = 'thelia.api.admin_resources';

    public function process(ContainerBuilder $container): void
    {
        $declared = $container->hasParameter(self::PARAMETER) ? (array) $container->getParameter(self::PARAMETER) : [];

        $container->setParameter(self::PARAMETER, [
            ...$declared,
            Dealer::class => DealerModule::RESOURCES_DEALER,
            DealerContact::class => DealerModule::RESOURCES_CONTACT,
            DealerContactInfo::class => DealerModule::RESOURCES_CONTACT,
            DealerSchedule::class => DealerModule::RESOURCES_SCHEDULES,
            DealerPickupConfig::class => DealerModule::RESOURCES_DEALER,
        ]);
    }
}
