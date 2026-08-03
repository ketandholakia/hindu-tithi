<?php

declare(strict_types=1);

namespace App\Services;

use UnitEnum;
use Vittix\Panchang\Localization\ArrayTranslator;

/**
 * Laravel-aware wrapper around the Panchang ArrayTranslator.
 *
 * Reads the active language from the current session (key: 'lang', default: 'en').
 * Use translate() in controllers to localise any Panchang enum value.
 */
final class PanchangTranslator
{
    /** Languages bundled with vittix/panchang */
    public const SUPPORTED = [
        'en' => 'English',
        'hi' => 'हिन्दी',
        'gu' => 'ગુજરાતી',
        'mr' => 'मराठी',
        'bn' => 'বাংলা',
        'ta' => 'தமிழ்',
        'te' => 'తెలుగు',
        'kn' => 'ಕನ್ನಡ',
        'ml' => 'മലയാളം',
        'sa' => 'संस्कृतम्',
    ];

    private ArrayTranslator $translator;

    public function __construct(private readonly string $lang = 'en')
    {
        // The package stores translation files alongside ArrayTranslator.php
        $basePath = dirname((new \ReflectionClass(ArrayTranslator::class))->getFileName());
        $this->translator = new ArrayTranslator($basePath);
    }

    /**
     * Build an instance from the current Laravel session lang value.
     */
    public static function fromSession(): self
    {
        $lang = session('lang', 'en');

        return new self(self::validLang($lang));
    }

    /**
     * Translate a Panchang enum value using the active language.
     * Falls back to English if the key is missing in the chosen language.
     * Returns null when $value is null (pass-through).
     */
    public function translate(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof UnitEnum) {
            return $this->translator->translate($value, $this->lang);
        }

        // Non-enum scalars (e.g. int pada, bool isAdhikaMasa) — return as-is
        return (string) $value;
    }

    /**
     * Translate with an explicit language code (useful in the API controller).
     */
    public static function translateWith(mixed $value, string $lang): ?string
    {
        return (new self(self::validLang($lang)))->translate($value);
    }

    public function lang(): string
    {
        return $this->lang;
    }

    /**
     * Validate and normalise a lang code, defaulting to 'en' if unknown.
     */
    public static function validLang(string $lang): string
    {
        return array_key_exists($lang, self::SUPPORTED) ? $lang : 'en';
    }
}
