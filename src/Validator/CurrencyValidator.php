<?php

declare(strict_types=1);

namespace ACE\Money\Validator;

use Laminas\Validator\AbstractValidator;
use Money\Currencies;
use Money\Currency;

use function is_string;
use function preg_match;

class CurrencyValidator extends AbstractValidator
{
    public const INVALID_TYPE = 'invalidType';
    public const INVALID_CODE = 'invalidCode';
    public const CODE_NOT_ACCEPTABLE = 'codeNotAcceptable';

    /** @var string[] */
    protected $messageTemplates = [
        self::INVALID_TYPE => 'Currency code should be a string',
        self::INVALID_CODE => 'Currency codes should be 3 letter uppercase strings. Received %value%',
        self::CODE_NOT_ACCEPTABLE => 'The currency "%value%" is not available in the list of allowable currency codes',
    ];

    /** @var Currencies */
    private $currencies;

    public function __construct(Currencies $allowedCurrencyCodes)
    {
        $this->currencies = $allowedCurrencyCodes;

        parent::__construct();
    }

    /** @inheritDoc */
    public function isValid($value): bool
    {
        if ($value instanceof Currency && ! $value->isAvailableWithin($this->currencies)) {
            $this->setValue($value->getCode());
            $this->error(self::CODE_NOT_ACCEPTABLE);

            return false;
        }

        if ($value instanceof Currency) {
            return true;
        }

        if (! is_string($value)) {
            $this->error(self::INVALID_TYPE);

            return false;
        }

        $this->setValue($value);
        if (! preg_match('/^[A-Z]{3}$/', $value)) {
            $this->error(self::INVALID_CODE);

            return false;
        }

        $currency = new Currency($value);
        if (! $currency->isAvailableWithin($this->currencies)) {
            $this->error(self::CODE_NOT_ACCEPTABLE);

            return false;
        }

        return true;
    }
}
