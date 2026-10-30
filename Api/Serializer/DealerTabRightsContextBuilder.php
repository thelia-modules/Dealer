<?php

declare(strict_types=1);

namespace Dealer\Api\Serializer;

use ApiPlatform\State\SerializerContextBuilderInterface;
use Dealer\Api\Resource\Dealer;
use Dealer\Dealer as DealerModule;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\HttpFoundation\Request;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\SecurityContext;
use Thelia\Model\Admin;

/**
 * The detail of a store embeds its contacts and opening hours, which the back office shows in
 * tabs of their own, each behind its own right. The core permission check only knows the right
 * of the endpoint (`admin.dealer`): the embedded tabs are dropped here when the administrator may
 * not view them, as their own endpoints would refuse them.
 */
#[AsDecorator(decorates: 'api_platform.serializer.context_builder')]
final readonly class DealerTabRightsContextBuilder implements SerializerContextBuilderInterface
{
    private const GROUP_RESOURCES = [
        Dealer::GROUP_ADMIN_READ_CONTACTS => DealerModule::RESOURCES_CONTACT,
        Dealer::GROUP_ADMIN_READ_SCHEDULES => DealerModule::RESOURCES_SCHEDULES,
    ];

    public function __construct(
        #[AutowireDecorated]
        private SerializerContextBuilderInterface $decorated,
        private Security $security,
        private SecurityContext $securityContext,
    ) {
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);

        if (!$normalization || Dealer::class !== ($context['resource_class'] ?? null) || !\is_array($context['groups'] ?? null)) {
            return $context;
        }

        $admin = $this->security->getUser();
        $context['groups'] = array_values(array_filter(
            $context['groups'],
            fn (string $group): bool => !isset(self::GROUP_RESOURCES[$group])
                || ($admin instanceof Admin && $this->securityContext->isUserGranted(['ADMIN'], [self::GROUP_RESOURCES[$group]], [], [AccessManager::VIEW], $admin)),
        ));

        return $context;
    }
}
