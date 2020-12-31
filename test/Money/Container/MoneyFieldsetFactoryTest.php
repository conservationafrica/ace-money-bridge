<?php

declare(strict_types=1);

namespace ACETest\Money\Container;

use ACE\Money\Form\MoneyFieldset;
use ACETest\Money\TestCase;
use Money\Currency;

class MoneyFieldsetFactoryTest extends TestCase
{
    private function getFieldsetFromContainer(): MoneyFieldset
    {
        $container = $this->getContainer();
        $forms = $container->get('FormElementManager');

        return $forms->get(MoneyFieldset::class);
    }

    public function testFieldsetCanBeRetrievedFromFormManager(): void
    {
        $fieldset = $this->getFieldsetFromContainer();
        $this->assertInstanceOf(MoneyFieldset::class, $fieldset);
    }

    public function testDefaultCurrencyIsPopulatedAsCurrencyValue(): void
    {
        $fieldset = $this->getFieldsetFromContainer();
        $default = $this->getContainer()->get(Currency::class);
        $this->assertSame($default->getCode(), $fieldset->get('currency')->getValue());
    }
}
