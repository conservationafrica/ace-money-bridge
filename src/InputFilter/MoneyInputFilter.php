<?php

declare(strict_types=1);

namespace ACE\Money\InputFilter;

use ACE\Money\Validator\CurrencyValidator;
use Laminas\Filter\Callback;
use Laminas\Filter\StringToUpper;
use Laminas\Filter\StringTrim;
use Laminas\Validator\Regex;

class MoneyInputFilter extends RequireableInputFilter
{
    public function init(): void
    {
        parent::init();

        $this->add([
            'name' => 'currency',
            'required' => true,
            'validators' => [
                'currency' => [
                    'name' => CurrencyValidator::class,
                ],
            ],
            'filters' => [
                'trim' => [
                    'name' => StringTrim::class,
                ],
                'upper' => [
                    'name' => StringToUpper::class,
                ],
            ],
        ]);
        $this->add([
            'name' => 'amount',
            'required' => true,
            'validators' => [
                'float' => [
                    'name' => Regex::class,
                    'options' => [
                        'pattern' => '(^-?\d*(\.\d+)?$)',
                        'messages' => [
                            Regex::NOT_MATCH => 'Monetary values should be positive or negative floating point ' .
                                'numbers with or without a decimal point and should not include thousand separators',
                        ],
                    ],
                ],
            ],
            'filters' => [
                'trim' => [
                    'name' => StringTrim::class,
                ],
                'toString' => [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => static function ($value): string {
                            return (string) $value;
                        },
                    ],
                ],
            ],
        ]);
    }
}
