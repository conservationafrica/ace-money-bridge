<?php

declare(strict_types=1);

namespace ACETest\Money\Validator;

use ACE\Money\Validator\CurrencyValidator;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use PHPUnit\Framework\TestCase;

use function assert;

class CurrencyValidatorTest extends TestCase
{
    /** @var CurrencyValidator */
    private $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new CurrencyValidator(new ISOCurrencies());
    }

    public function testNonString(): void
    {
        self::assertFalse($this->validator->isValid(1));
        self::assertArrayHasKey(CurrencyValidator::INVALID_TYPE, $this->validator->getMessages());
    }

    /** @return array<string[]> */
    public function invalidStringCodes(): array
    {
        return [
            ['a'], // Too Short
            ['aaaa'], // Too Long
            ['aaa'], // Lowercase
            ['!!!'], // Not Alpha
            ['123'], // Not alpha
        ];
    }

    /** @dataProvider invalidStringCodes */
    public function testInvalidCode(string $code): void
    {
        self::assertFalse($this->validator->isValid($code));
        self::assertArrayHasKey(CurrencyValidator::INVALID_CODE, $this->validator->getMessages());
    }

    public function testCurrencyInstanceIsValid(): void
    {
        self::assertTrue($this->validator->isValid(new Currency('GBP')));
    }

    public function testCurrencyInstanceMustBeInAllowableList(): void
    {
        self::assertFalse($this->validator->isValid(new Currency('ZZZ')));
        self::assertArrayHasKey(CurrencyValidator::CODE_NOT_ACCEPTABLE, $this->validator->getMessages());
        self::assertStringContainsString(
            'The currency "ZZZ" is not available',
            $this->validator->getMessages()[CurrencyValidator::CODE_NOT_ACCEPTABLE],
        );
    }

    /** @return iterable<string[]> */
    public function validCodes(): iterable
    {
        foreach ((new ISOCurrencies())->getIterator() as $currency) {
            assert($currency instanceof Currency);

            yield [$currency->getCode()];
        }
    }

    /** @dataProvider validCodes */
    public function testValidCodes(string $code): void
    {
        self::assertTrue($this->validator->isValid($code));
    }

    public function testCodeNotInListIsInvalid(): void
    {
        self::assertFalse($this->validator->isValid('ZZZ'));
        self::assertArrayHasKey(CurrencyValidator::CODE_NOT_ACCEPTABLE, $this->validator->getMessages());
        self::assertStringContainsString(
            'The currency "ZZZ" is not available',
            $this->validator->getMessages()[CurrencyValidator::CODE_NOT_ACCEPTABLE],
        );
    }
}
