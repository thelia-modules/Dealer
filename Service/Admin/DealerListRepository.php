<?php

declare(strict_types=1);

namespace Dealer\Service\Admin;

use Dealer\Model\Dealer;
use Dealer\Model\DealerQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Model\Country;
use Thelia\Model\CountryQuery;

final readonly class DealerListRepository
{
    public const SORT_FIELDS = ['title', 'city', 'id'];

    private const I18N_ALIAS = 'dealer_i18n_list';

    /**
     * @return array{rows: list<Dealer>, total: int, lastPage: int}
     */
    public function findPaginated(
        DealerListFilters $filters,
        string $sortField,
        string $sortDirection,
        string $locale,
        int $page,
        int $pageSize,
    ): array {
        $query = DealerQuery::create()
            ->leftJoinDealerI18n(self::I18N_ALIAS)
            ->addJoinCondition(self::I18N_ALIAS, self::I18N_ALIAS.'.Locale = ?', $locale, null, \PDO::PARAM_STR)
            ->withColumn(self::I18N_ALIAS.'.Title', 'i18n_title');

        $filters->applyTo($query, self::I18N_ALIAS);

        $direction = 'desc' === $sortDirection ? Criteria::DESC : Criteria::ASC;
        match ($sortField) {
            'title' => $query->orderBy('i18n_title', $direction),
            'city' => $query->orderByCity($direction)->orderByZipcode($direction),
            default => null,
        };
        $query->orderById('id' === $sortField ? $direction : Criteria::ASC);

        $pager = $query->paginate($page, $pageSize);

        return [
            'rows' => iterator_to_array($pager->getResults(), false),
            'total' => $pager->getNbResults(),
            'lastPage' => max(1, $pager->getLastPage()),
        ];
    }

    /**
     * @param list<int> $countryIds
     *
     * @return array<int, Country> the countries of a page, in one query
     */
    public function findCountries(array $countryIds, string $locale): array
    {
        if ([] === $countryIds) {
            return [];
        }

        $countries = [];
        foreach (CountryQuery::create()->filterById(array_unique($countryIds))->joinWithI18n($locale)->find() as $country) {
            $countries[$country->getId()] = $country;
        }

        return $countries;
    }
}
