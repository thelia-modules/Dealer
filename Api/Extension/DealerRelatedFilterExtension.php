<?php

declare(strict_types=1);

namespace Dealer\Api\Extension;

use ApiPlatform\Metadata\Operation;
use Dealer\Api\Resource\Dealer;
use Dealer\Model\DealerQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Api\Bridge\Propel\Extension\QueryCollectionExtensionInterface;

/**
 * Filters the dealer collection by the entities they are linked to,
 * through the dealer_content / dealer_folder / dealer_brand / dealer_product
 * join tables. Each parameter accepts a single id or a comma-separated list.
 *
 * GET /api/front/dealers?contentId=5
 * GET /api/front/dealers?brandId=1,2,3
 */
final readonly class DealerRelatedFilterExtension implements QueryCollectionExtensionInterface
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function applyToCollection(
        ModelCriteria $query,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if (Dealer::class !== $resourceClass || !$query instanceof DealerQuery) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return;
        }

        $contentIds = $this->extractIds($request, 'contentId');
        if ([] !== $contentIds) {
            $query
                ->useDealerContentQuery()
                    ->filterByContentId($contentIds, Criteria::IN)
                ->endUse();
        }

        $folderIds = $this->extractIds($request, 'folderId');
        if ([] !== $folderIds) {
            $query
                ->useDealerFolderQuery()
                    ->filterByFolderId($folderIds, Criteria::IN)
                ->endUse();
        }

        $brandIds = $this->extractIds($request, 'brandId');
        if ([] !== $brandIds) {
            $query
                ->useDealerBrandQuery()
                    ->filterByBrandId($brandIds, Criteria::IN)
                ->endUse();
        }

        $productIds = $this->extractIds($request, 'productId');
        if ([] !== $productIds) {
            $query
                ->useDealerProductQuery()
                    ->filterByProductId($productIds, Criteria::IN)
                ->endUse();
        }
    }

    /**
     * @return int[]
     */
    private function extractIds(Request $request, string $parameter): array
    {
        $value = $request->query->all()[$parameter] ?? null;

        if (null === $value || '' === $value) {
            return [];
        }

        $raw = \is_array($value) ? $value : explode(',', (string) $value);

        return array_values(array_filter(array_map(
            static fn ($id): int => (int) $id,
            $raw,
        )));
    }
}
