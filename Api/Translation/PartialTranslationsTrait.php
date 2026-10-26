<?php

declare(strict_types=1);

namespace Dealer\Api\Translation;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Thelia\Api\Resource\I18n;
use Thelia\Model\LangQuery;

/**
 * Translations written field by field, for a resource extending AbstractTranslatableResource.
 *
 * The core setI18ns() replaces a locale with a fresh object built from the payload alone, and
 * the bridge then writes every field of it: a PATCH sending only `description` blanked the title
 * and every other field of that locale. A PATCH is read first with the locales it names, so
 * merging the payload into what was read keeps the fields it does not send.
 *
 * Only the declared translated fields are written, and only as text: the payload reaches this
 * setter raw, past the serializer's type checks.
 *
 * The using class declares REQUIRED_I18N_FIELD, the translated column that cannot be null.
 */
trait PartialTranslationsTrait
{
    /** @var array<string, list<string>> translated fields the payload wrote, by locale */
    #[Ignore]
    private array $writtenTranslationFields = [];

    /** @return array<string, list<string>> */
    #[Ignore]
    public function getWrittenTranslationFields(): array
    {
        return $this->writtenTranslationFields;
    }

    public function setI18ns(array $i18ns): self
    {
        $i18nClass = static::getI18nResourceClass();
        $writableFields = array_values(array_filter(
            array_map(static fn (\ReflectionProperty $property): string => $property->getName(), (new \ReflectionClass($i18nClass))->getProperties()),
            static fn (string $field): bool => 'id' !== $field,
        ));

        foreach ($i18ns as $locale => $fields) {
            if (!\is_array($fields)) {
                throw new UnprocessableEntityHttpException(sprintf('i18ns.%s must be an object.', $locale));
            }

            $read = $this->i18ns->i18ns[$locale] ?? null;

            if (!$read instanceof I18n) {
                $read = new $i18nClass();
                $this->i18ns->add($read, (string) $locale);
            }

            foreach ($fields as $field => $value) {
                if (!\in_array($field, $writableFields, true)) {
                    throw new UnprocessableEntityHttpException(sprintf('i18ns.%s.%s is not a translated field (%s).', $locale, $field, implode(', ', $writableFields)));
                }

                if (null !== $value && !\is_string($value)) {
                    throw new UnprocessableEntityHttpException(sprintf('i18ns.%s.%s must be a string or null.', $locale, $field));
                }

                $read->{'set'.ucfirst($field)}($value);
                $this->writtenTranslationFields[(string) $locale][] = $field;
            }
        }

        return $this;
    }

    #[Assert\Callback(groups: [self::GROUP_ADMIN_WRITE])]
    public function validateTranslations(ExecutionContextInterface $context): void
    {
        // The reads answer the active languages only: a translation written in another one
        // would be stored, then never shown by the API nor by the back office.
        $shopLocales = LangQuery::create()->filterByActive(1)->select(['Locale'])->find()->getData();
        $getter = 'get'.ucfirst(self::REQUIRED_I18N_FIELD);
        $locales = 0;

        foreach ($this->i18ns as $locale => $i18n) {
            ++$locales;

            if (!\in_array($locale, $shopLocales, true)) {
                $context->buildViolation(sprintf('Unknown locale "%s": the shop has %s.', $locale, implode(', ', $shopLocales)))
                    ->atPath('i18ns')
                    ->addViolation();

                continue;
            }

            if ('' === trim((string) $i18n->{$getter}())) {
                $context->buildViolation(sprintf('%s is required in %s.', self::REQUIRED_I18N_FIELD, $locale))
                    ->atPath('i18ns['.$locale.'].'.self::REQUIRED_I18N_FIELD)
                    ->addViolation();
            }
        }

        if (0 === $locales && null === $this->getId()) {
            $context->buildViolation(sprintf('%s is required in at least one locale.', self::REQUIRED_I18N_FIELD))
                ->atPath('i18ns')
                ->addViolation();
        }
    }
}
