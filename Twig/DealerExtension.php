<?php

namespace Dealer\Twig;


use Dealer\Dealer;
use Symfony\Contracts\Translation\TranslatorInterface;
use Thelia\Api\Service\DataAccess\DataAccessService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class DealerExtension extends AbstractExtension {

    public function __construct(
        private readonly DataAccessService $dataAccessService,
        protected ?TranslatorInterface $translator
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getDealers', [$this, 'getDealers']),
            new TwigFunction('getDealer', [$this, 'getDealer']),
            new TwigFunction('getDays', [$this, 'getDays']),
        ];
    }
    public function getDealers(array $params = []): array|object|null
    {
        return $this->dataAccessService->resources('/api/front/dealers', $params);
    }

    public function getDealer(int $id): array|object|null
    {
        return $this->dataAccessService->resources('/api/front/dealers/'.$id);
    }

    public function getDays(): array
    {
        return [
            $this->translator->trans("Monday", [], Dealer::MESSAGE_DOMAIN),
            $this->translator->trans("Tuesday", [], Dealer::MESSAGE_DOMAIN),
            $this->translator->trans("Wednesday", [], Dealer::MESSAGE_DOMAIN),
            $this->translator->trans("Thursday", [], Dealer::MESSAGE_DOMAIN),
            $this->translator->trans("Friday", [], Dealer::MESSAGE_DOMAIN),
            $this->translator->trans("Saturday", [], Dealer::MESSAGE_DOMAIN),
            $this->translator->trans("Sunday", [], Dealer::MESSAGE_DOMAIN)
        ];
    }
}


