<?php

declare(strict_types=1);

namespace ACETest\Money\Hydrator;

use ACE\Money\Exception\InvalidArgumentException;
use ACE\Money\Hydrator\MoneyHydrator;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\Parser\DecimalMoneyParser;
use PHPUnit\Framework\TestCase;
use stdClass;

class MoneyHydratorTest extends TestCase
{
    /** @var MoneyHydrator */
    private $hydrator;

    protected function setUp(): void
    {
        parent::setUp();
        $list = new ISOCurrencies();
        $this->hydrator = new MoneyHydrator(
            new DecimalMoneyFormatter($list),
            new DecimalMoneyParser($list),
        );
    }

    /** @return mixed[] */
    public function invalidExtractionTypes(): array
    {
        return [
            ['Foo'],
            [1],
            [new stdClass()],
            [[]],
            [null],
        ];
    }

    /** @dataProvider invalidExtractionTypes */
    public function testExtractThrowsExceptionForNonMoney($arg): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected an instance of');
        $this->hydrator->extract($arg);
    }

    public function testExtraction(): void
    {
        $money = new Money(1000, new Currency('GBP'));
        $data = $this->hydrator->extract($money);
        self::assertArrayHasKey('currency', $data);
        self::assertArrayHasKey('amount', $data);
        self::assertEquals('10.00', $data['amount']);
        self::assertEquals('GBP', $data['currency']);
    }

    public function testHydrationThrowsExceptionForMissingCurrency(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected the array key \'currency\'');
        $this->hydrator->hydrate(['amount' => '10.00'], null);
    }

    public function testHydrationThrowsExceptionForMissingAmount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected the array key \'amount\'');
        $this->hydrator->hydrate(['currency' => 'GBP'], null);
    }

    public function testReturnValueIsExpectedMoneyInstance(): void
    {
        $money = $this->hydrator->hydrate([
            'currency' => 'GBP',
            'amount' => '10.00',
        ], null);
        self::assertEquals('GBP', $money->getCurrency()->getCode());
        self::assertEquals(1000, $money->getAmount());
    }

    public function testHydratedObjectIsNotTheSameAsTheHydrationArgument(): void
    {
        $arg = new Money(2000, new Currency('USD'));
        $money = $this->hydrator->hydrate([
            'currency' => 'GBP',
            'amount' => '10.00',
        ], $arg);
        self::assertNotSame($arg, $money);
    }
}
