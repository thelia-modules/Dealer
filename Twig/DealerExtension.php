<?php

namespace Dealer\Twig;


use Thelia\Api\Service\DataAccess\DataAccessService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class DealerExtension extends AbstractExtension {

    public function __construct(
        private readonly DataAccessService $dataAccessService,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getDealers', [$this, 'getDealers']),
            new TwigFunction('getDealer', [$this, 'getDealer']),
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
}


