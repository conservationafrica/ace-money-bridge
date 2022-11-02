<?php

declare(strict_types=1);

namespace ACETest\Money\Form;

use ACE\Money\Form\MoneyFieldset;
use ACE\Money\Hydrator\MoneyHydrator;
use ACETest\Money\BindableObject;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Laminas\Hydrator\ClassMethodsHydrator;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\Parser\DecimalMoneyParser;
use PHPUnit\Framework\TestCase;

use function assert;

class MoneyFieldsetTest extends TestCase
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

    public function testConstructorAddsFormElements(): void
    {
        $currency = new Text();
        $amount = new Text();
        $fieldset = new MoneyFieldset($currency, $amount, $this->hydrator, new Currency('GBP'));
        $this->assertSame('currency', $currency->getName());
        $this->assertSame('amount', $amount->getName());
        $this->assertContains($currency, $fieldset->getElements());
        $this->assertContains($amount, $fieldset->getElements());
    }

    private function fieldset(): MoneyFieldset
    {
        $currency = new Text();
        $amount = new Text();

        return new MoneyFieldset($currency, $amount, $this->hydrator, new Currency('GBP'));
    }

    private function form(): Form
    {
        $form = new Form();
        $fieldset = $this->fieldset();
        $fieldset->setName('amount');
        $form->add($fieldset);

        return $form;
    }

    public function testFieldsetBinding(): void
    {
        $form = $this->form();
        $form->setHydrator(new ClassMethodsHydrator());
        $bind = new BindableObject();
        $money = new Money(2000, new Currency('GBP'));
        $bind->setAmount($money);
        $form->bind($bind);
        $form->isValid();
        $value = $form->getData();
        $this->assertObjectHasAttribute('amount', $value);
        $this->assertInstanceOf(Money::class, $value->amount);
        $moneyProperty = $value->amount;
        assert($moneyProperty instanceof Money);
        $this->assertNotSame($money, $moneyProperty);
        $this->assertEquals('GBP', $moneyProperty->getCurrency()->getCode());
        $this->assertEquals(2000, $moneyProperty->getAmount());
    }

    public function testValidatedFormValuesAreReflectedInObject(): void
    {
        $form = $this->form();
        $form->setHydrator(new ClassMethodsHydrator());
        $bind = new BindableObject();
        $form->bind($bind);
        $form->setData([
            'amount' => [
                'currency' => 'ZAR',
                'amount' => '123.45',
            ],
        ]);
        $this->assertTrue($form->isValid());
        $object = $form->getData();
        $this->assertInstanceOf(Money::class, $object->amount);
        $moneyProperty = $object->amount;
        assert($moneyProperty instanceof Money);
        $this->assertEquals('ZAR', $moneyProperty->getCurrency()->getCode());
        $this->assertEquals(12345, $moneyProperty->getAmount());
    }

    public function testRetrievalOfCurrencyElement(): void
    {
        $fieldset = $this->fieldset();
        $currency = $fieldset->currencyElement();
        $this->assertSame('currency', $currency->getName());
    }

    public function testRetrievalOfAmountElement(): void
    {
        $fieldset = $this->fieldset();
        $amount = $fieldset->amountElement();
        $this->assertSame('amount', $amount->getName());
    }
}
