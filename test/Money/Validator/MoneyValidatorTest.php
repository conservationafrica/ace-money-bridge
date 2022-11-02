<?php

declare(strict_types=1);

namespace ACETest\Money\Validator;

use ACE\Money\Validator\CurrencyValidator;
use ACE\Money\Validator\MoneyValidator;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class MoneyValidatorTest extends TestCase
{
    /** @var MoneyValidator */
    private $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new MoneyValidator(
            new CurrencyValidator(
                new ISOCurrencies(),
            ),
        );
    }

    /** @return mixed[] */
    public function validValues(): array
    {
        return [
            ['1.00', 'GBP', 100, 'GBP'],
            [1.10, 'GBP', 110, 'GBP'],
            ['123', 'GBP', 12300, 'GBP'],
            ['0', 'GBP', 0, 'GBP'],
            ['0.0', 'GBP', 0, 'GBP'],
            [0, 'GBP', 0, 'GBP'],
            [0.0, 'GBP', 0, 'GBP'],
            ['-1.00', 'GBP', -100, 'GBP'],
            [-1, 'GBP', -100, 'GBP'],
        ];
    }

    /** @return mixed[] */
    public function invalidInput(): array
    {
        return [
            [[]],
            ['foo'],
            [1],
            [null],
            [1.9],
            [['currency' => '', 'amount' => '']],
            [['currency' => 'GBP', 'amount' => '']],
            [['currency' => 'GBP', 'amount' => null]],
            [['currency' => '123', 'amount' => 'GBP']],
            [['currency' => '123', 'amount' => 'GBP']],
        ];
    }

    /**
     * @param mixed $inputAmount
     * @param mixed $inputCode
     *
     * @dataProvider validValues
     */
    public function testValidValues($inputAmount, $inputCode, int $expectAmount, string $expectCode): void
    {
        $input = [
            'currency' => $inputCode,
            'amount' => $inputAmount,
        ];
        $this->assertTrue(
            $this->validator->isValid($input),
        );
    }

    /**
     * @param mixed $input
     *
     * @dataProvider invalidInput
     */
    public function testInvalidInput($input): void
    {
        $this->assertFalse(
            $this->validator->isValid($input),
        );
    }

    public function testMoneyInstanceWillBeValid(): void
    {
        $input = new Money(100, new Currency('GBP'));
        $this->assertTrue(
            $this->validator->isValid($input),
        );
    }
}
