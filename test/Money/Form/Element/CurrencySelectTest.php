<?php

declare(strict_types=1);

namespace ACETest\Money\Form\Element;

use ACE\Money\Form\Element\CurrencySelect;
use ACETest\Money\TestCase;
use Laminas\ServiceManager\AbstractPluginManager;
use Money\Currencies\ISOCurrencies;

use function assert;

class CurrencySelectTest extends TestCase
{
    public function testSelectContainsExpectedOptions(): void
    {
        $select = new CurrencySelect(new ISOCurrencies());
        self::assertContains('GBP', $select->getValueOptions());
    }

    public function testElementCanBeRetrievedFromFormManager(): void
    {
        $forms = $this->getContainer()->get('FormElementManager');
        assert($forms instanceof AbstractPluginManager);
        $element = $forms->get(CurrencySelect::class);
        self::assertInstanceOf(CurrencySelect::class, $element);
    }
}
