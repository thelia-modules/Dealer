<?php

declare(strict_types=1);

namespace Dealer\Service\Admin;

use Dealer\Model\DealerQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\HttpFoundation\Request;

/**
 * Filters of the store list, read from the query string so that every state of the list has a URL.
 */
final readonly class DealerListFilters
{
    private const SEARCH_MAX_LENGTH = 100;

    private function __construct(
        public string $search,
        public ?DealerVisibilityFilter $visibility,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $search = trim((string) $request->query->get('q', ''));

        return new self(
            mb_substr($search, 0, self::SEARCH_MAX_LENGTH),
            DealerVisibilityFilter::tryFrom((string) $request->query->get('visible', '')),
        );
    }

    /**
     * The search reads the store name in the interface locale, through the translation join
     * the caller declared under $i18nAlias.
     */
    public function applyTo(DealerQuery $query, string $i18nAlias): void
    {
        if (null !== $this->visibility) {
            $query->filterByVisible($this->visibility->storedValue());
        }

        if ('' === $this->search) {
            return;
        }

        $pattern = '%'.addcslashes($this->search, '%_\\').'%';
        $conditions = [
            'search_title' => $i18nAlias.'.Title LIKE ?',
            'search_address' => 'Dealer.Address1 LIKE ?',
            'search_zipcode' => 'Dealer.Zipcode LIKE ?',
            'search_city' => 'Dealer.City LIKE ?',
        ];

        foreach ($conditions as $name => $clause) {
            $query->condition($name, $clause, $pattern, \PDO::PARAM_STR);
        }

        $query->where(array_keys($conditions), Criteria::LOGICAL_OR);
    }

    /** @return array<string, string> */
    public function toQueryParams(): array
    {
        return array_filter([
            'q' => $this->search,
            'visible' => $this->visibility?->value ?? '',
        ], static fn (string $value): bool => '' !== $value);
    }

    public function withVisibility(?DealerVisibilityFilter $visibility): self
    {
        return new self($this->search, $visibility);
    }
}
