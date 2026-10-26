<?php

declare(strict_types=1);

namespace Dealer\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Dealer\Api\Resource\Dealer;
use Dealer\Api\Resource\DealerI18n;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * The two descriptions of a store are HTML that a storefront renders as is: written by a
 * third-party tool, they are reduced to the safe elements first, scripts, event handlers and
 * javascript: links dropped. Only the fields the payload sends: a description written in the
 * back office and left out of the request stays exactly as it is.
 */
final readonly class DealerPersistProcessor implements ProcessorInterface
{
    private HtmlSanitizer $htmlSanitizer;

    public function __construct(
        private DealerScopedPersistProcessor $dealerScopedPersistProcessor,
    ) {
        $this->htmlSanitizer = new HtmlSanitizer(
            (new HtmlSanitizerConfig())->allowSafeElements()->allowRelativeLinks()->allowRelativeMedias(),
        );
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof Dealer) {
            $written = $data->getWrittenTranslationFields();

            foreach ($data->getI18ns() as $locale => $i18n) {
                if (!$i18n instanceof DealerI18n) {
                    continue;
                }

                if (\in_array('description', $written[$locale] ?? [], true)) {
                    $i18n->setDescription($this->sanitize($i18n->getDescription()));
                }

                if (\in_array('bigDescription', $written[$locale] ?? [], true)) {
                    $i18n->setBigDescription($this->sanitize($i18n->getBigDescription()));
                }
            }
        }

        return $this->dealerScopedPersistProcessor->process($data, $operation, $uriVariables, $context);
    }

    private function sanitize(?string $html): ?string
    {
        return null === $html ? null : $this->htmlSanitizer->sanitize($html);
    }
}
