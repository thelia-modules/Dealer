<?php

declare(strict_types=1);

namespace Dealer\Service\Admin;

use BackOfficeDefaultTwigBundle\Service\I18n\CountryFlag;
use BackOfficeDefaultTwigBundle\UiComponents\DataTable\RowAction;
use Dealer\Dealer as DealerModule;
use Dealer\Model\Dealer;
use Symfony\Contracts\Translation\TranslatorInterface;
use Thelia\Core\Security\AccessManager;
use Thelia\Model\Country;
use Thelia\Tools\URL;

/**
 * Shapes the stores of a page into the flat rows the back-office BoDataTable renders.
 */
final readonly class DealerListRowPresenter
{
    private const DOMAIN = 'dealer.bo.default-twig';
    private const LIST_PATH = '/admin/module/Dealer/dealer';
    private const GRANTED_SUBJECT = ['resource' => 'admin.module', 'module' => 'Dealer'];

    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @param list<Dealer>          $dealers
     * @param array<int, Country>   $countries   by id, already translated in $locale
     * @param array<string, string> $queryParams state of the list, kept by the visibility switch
     *
     * @return list<array<string, mixed>>
     */
    public function presentAll(array $dealers, array $countries, string $locale, bool $canUpdate, array $queryParams): array
    {
        return array_map(
            fn (Dealer $dealer): array => $this->present($dealer, $countries[$dealer->getCountryId()] ?? null, $locale, $canUpdate, $queryParams),
            $dealers,
        );
    }

    /**
     * @param array<string, string> $queryParams
     *
     * @return array<string, mixed>
     */
    private function present(Dealer $dealer, ?Country $country, string $locale, bool $canUpdate, array $queryParams): array
    {
        $id = (int) $dealer->getId();
        $title = trim((string) $dealer->getVirtualColumn('i18n_title'));
        $editUrl = URL::getInstance()->absoluteUrl(self::LIST_PATH.'/edit', ['dealer_id' => $id]);

        return [
            'id' => $id,
            'title_html' => $this->renderTitle($id, $title, $editUrl),
            'address_html' => $this->renderAddress($dealer, $country, $locale),
            'visible_html' => $this->renderVisibilitySwitch(
                $id,
                '' !== $title ? $title : '#'.$id,
                1 === (int) $dealer->getVisible(),
                $canUpdate ? URL::getInstance()->absoluteUrl(self::LIST_PATH.'/toggle-online/'.$id, $queryParams) : null,
            ),
            '_actions' => [
                new RowAction(
                    kind: 'edit',
                    label: $this->translator->trans('Edit this Dealer', [], self::DOMAIN),
                    href: $editUrl,
                    grantedAttribute: AccessManager::UPDATE,
                    grantedSubject: self::GRANTED_SUBJECT,
                ),
                new RowAction(
                    kind: 'delete',
                    label: $this->translator->trans('Delete this Dealer', [], self::DOMAIN),
                    modalTarget: '#dealer-delete',
                    grantedAttribute: AccessManager::DELETE,
                    grantedSubject: self::GRANTED_SUBJECT,
                    dataAttributes: ['dealer-id' => (string) $id, 'dealer-title' => '' !== $title ? $title : '#'.$id],
                ),
            ],
        ];
    }

    /**
     * A switch, as the list always had. A click is sent as a POST carrying the back-office token
     * by the theme's `data-bo-post` controller, which then reloads the list in its state.
     */
    private function renderVisibilitySwitch(int $id, string $title, bool $visible, ?string $toggleUrl): string
    {
        return \sprintf(
            '<div class="form-check form-switch d-inline-block mb-0"><input class="form-check-input" type="checkbox" role="switch" id="dealer-visible-%d" aria-label="%s"%s%s data-testid="dealer-visible-switch"></div>',
            $id,
            htmlspecialchars($this->translator->trans('Online', [], self::DOMAIN).' : '.$title),
            $visible ? ' checked' : '',
            null !== $toggleUrl ? ' data-bo-post data-bo-post-url="'.htmlspecialchars($toggleUrl).'"' : ' disabled',
        );
    }

    private function renderTitle(int $id, string $title, string $editUrl): string
    {
        $label = '' !== $title
            ? htmlspecialchars($title)
            : '<span class="fst-italic text-muted">'.htmlspecialchars($this->translator->trans('No name in this language', [], self::DOMAIN)).'</span>';

        return \sprintf(
            '<a href="%s" class="fw-semibold text-decoration-none" data-testid="dealer-edit-link">%s</a><div class="text-muted small">#%d</div>',
            htmlspecialchars($editUrl),
            $label,
            $id,
        );
    }

    private function renderAddress(Dealer $dealer, ?Country $country, string $locale): string
    {
        $street = trim((string) $dealer->getAddress1());
        $place = trim($dealer->getZipcode().' '.$dealer->getCity());
        $countryTitle = null !== $country ? trim((string) $country->setLocale($locale)->getTitle()) : '';
        $secondLine = implode(', ', array_filter([$place, $countryTitle], static fn (string $part): bool => '' !== $part));

        return \sprintf(
            '<div class="bo-order-customer"><div class="text-truncate">%s%s</div><div class="text-muted small text-truncate">%s</div></div>',
            $this->countryFlag($country?->getIsoalpha2()),
            htmlspecialchars('' !== $street ? $street : $place),
            htmlspecialchars('' !== $street ? $secondLine : $countryTitle),
        );
    }

    /**
     * The flag before the address, as the orders list shows it before a customer. The helper
     * belongs to the back-office theme and its older releases do not ship it: the same flag
     * (two regional indicator symbols) is then built here.
     */
    private function countryFlag(?string $isoAlpha2): string
    {
        if (class_exists(CountryFlag::class)) {
            return CountryFlag::prefix($isoAlpha2);
        }

        if (null === $isoAlpha2 || 1 !== preg_match('/^[A-Za-z]{2}$/', $isoAlpha2)) {
            return '';
        }

        $upper = strtoupper($isoAlpha2);
        $offset = 0x1F1E6 - \ord('A');

        return mb_chr(\ord($upper[0]) + $offset).mb_chr(\ord($upper[1]) + $offset).' ';
    }
}
