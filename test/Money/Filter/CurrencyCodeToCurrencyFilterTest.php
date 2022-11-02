<?php

declare(strict_types=1);

namespace ACETest\Money\Filter;

use ACE\Money\Filter\CurrencyCodeToCurrencyFilter;
use Money\Currency;
use PHPUnit\Framework\TestCase;
use stdClass;

class CurrencyCodeToCurrencyFilterTest extends TestCase
{
    /** @var CurrencyCodeToCurrencyFilter */
    private $filter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filter = new CurrencyCodeToCurrencyFilter();
    }

    /** @return list<array{0: mixed}> */
    public function dataThatShouldNotBeFiltered(): array
    {
        return [
            [''],
            [null],
            [0],
            [0.0],
            [[]],
            [new stdClass()],
            ['a'],
            ['aa'],
            ['aaaa'],
            ['111'],
            [111],
            ['A12'],
            ['AA1'],
        ];
    }

    /**
     * @param mixed $value
     *
     * @dataProvider dataThatShouldNotBeFiltered
     */
    public function testUnfilteredValues($value): void
    {
        /** @var mixed $filtered */
        $filtered = $this->filter->filter($value);
        self::assertSame($value, $filtered);
    }

    /** @return list<array{0: string, 1: string}> */
    public function dataThatShouldBeFiltered(): array
    {
        return [
            ['GBP', 'GBP'],
            [' gbp ', 'GBP'],
            ['AAA', 'AAA'],
            ['ZZZ', 'ZZZ'],
        ];
    }

    /** @dataProvider dataThatShouldBeFiltered */
    public function testFilteredValues(string $value, string $expect): void
    {
        $filtered = $this->filter->filter($value);
        self::assertInstanceOf(Currency::class, $filtered);
        self::assertSame($expect, $filtered->getCode());
    }

    public function testCurrencyInstancesAreReturnedAsIs(): void
    {
        $currency = new Currency('GBP');
        $filtered = $this->filter->filter($currency);
        self::assertSame($currency, $filtered);
    }
}
