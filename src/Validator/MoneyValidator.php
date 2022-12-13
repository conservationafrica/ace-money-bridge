<?php

declare(strict_types=1);

namespace ACE\Money\Validator;

use Laminas\Validator\AbstractValidator;
use Money\Money;

use function is_array;
use function is_numeric;

class MoneyValidator extends AbstractValidator
{
    public const NOT_ARRAY = 'inputNotArray';
    public const MISSING_KEYS = 'missingArrayKeys';
    public const INVALID_CURRENCY = 'invalidCurrency';
    public const NOT_NUMERIC = 'notANumber';

    /** @var CurrencyValidator */
    private $currencyValidator;

    /** @var string[] */
    protected $messageTemplates = [
        self::NOT_ARRAY => 'Expected the input to be an array',
        self::MISSING_KEYS => 'The input array should contain keys for "amount" and "currency"',
        self::INVALID_CURRENCY => 'You provided an invalid currency code',
        self::NOT_NUMERIC => 'The amount value should be numeric',
    ];

    public function __construct(CurrencyValidator $currencyValidator)
    {
        $this->currencyValidator = $currencyValidator;

        parent::__construct();
    }

    /** @inheritDoc */
    public function isValid($value): bool
    {
        if ($value instanceof Money) {
            return true;
        }

        if (! is_array($value)) {
            $this->error(self::NOT_ARRAY);

            return false;
        }

        if (! isset($value['amount'], $value['currency'])) {
            $this->error(self::MISSING_KEYS);

            return false;
        }

        if (! $this->currencyValidator->isValid($value['currency'])) {
            $this->error(self::INVALID_CURRENCY);

            return false;
        }

        if (! is_numeric($value['amount'])) {
            $this->error(self::NOT_NUMERIC);

            return false;
        }

        return true;
    }
}
