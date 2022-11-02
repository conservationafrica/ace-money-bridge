<?php

declare(strict_types=1);

namespace ACETest\Money\Form\Element;

use ACE\Money\Form\Element\MoneyElement;
use ACETest\Money\TestCase;
use Laminas\Form\Form;
use Money\Currency;
use Money\Money as MoneyValue;

use function json_encode;

class MoneyElementTest extends TestCase
{
    /** @var MoneyElement */
    private $element;

    /** @var Form */
    private $form;

    protected function setUp(): void
    {
        parent::setUp();
        $container = $this->getContainer();
        $forms = $container->get('FormElementManager');
        $this->element = $forms->get(MoneyElement::class);
    }

    private function prepareForm(): Form
    {
        $container = $this->getContainer();
        $forms = $container->get('FormElementManager');
        $this->form = $forms->get(Form::class);
        $this->element->setName('myMoney');
        $this->form->add($this->element);
        $this->form->prepare();

        return $this->form;
    }

    public function testElementsCanBeRetrieved(): void
    {
        self::assertNotNull($this->element->amountElement());
        self::assertNotNull($this->element->currencyElement());
    }

    public function testWeCanGetSomeMoneyOutOfTheForm(): void
    {
        $this->prepareForm();
        self::assertSame('myMoney', $this->element->getName());
        self::assertSame($this->element, $this->form->get('myMoney'));

        $input = [
            'myMoney' => [
                'amount' => '1.23',
                'currency' => 'GBP',
            ],
        ];

        $this->form->setData($input);
        self::assertTrue($this->form->isValid(), json_encode($this->form->getMessages()));
        $output = $this->form->getData();
        self::assertArrayHasKey('myMoney', $output);
        self::assertInstanceOf(MoneyValue::class, $output['myMoney']);
    }

    public function testMoneyElementInputIsOk(): void
    {
        $this->prepareForm();
        $money = new MoneyValue(123, new Currency('GBP'));
        $this->form->setData(['myMoney' => $money]);
        self::assertTrue($this->form->isValid());
        $out = $this->form->getData()['myMoney'];
        self::assertInstanceOf(MoneyValue::class, $out);
        self::assertTrue($money->equals($out));
    }

    public function testInvalidArrayInput(): void
    {
        $input = [
            'myMoney' => [
                'baz' => 'bat',
                'bing' => 'bong',
            ],
        ];
        $this->prepareForm();
        $this->form->setData($input);
        self::assertFalse($this->form->isValid());
        $out = $this->form->getData()['myMoney'];
        self::assertEquals($input['myMoney'], $out);
    }

    public function testStringValueCanBeRetrieved(): void
    {
        $input = [
            'myMoney' => [
                'amount' => '1.12',
                'currency' => 'GBP',
            ],
        ];
        $this->prepareForm();
        $this->form->setData($input);
        $value = $this->element->getValue();
        self::assertStringMatchesFormat('%s %f', $value);
    }

    public function testElementAttributesWillBeProvidedToElements(): void
    {
        $this->element->setOptions([
            'currency_attributes' => ['data-foo' => 'baz'],
            'amount_attributes' => ['data-bar' => 'bing'],
        ]);
        self::assertSame('baz', $this->element->currencyElement()->getAttribute('data-foo'));
        self::assertSame('bing', $this->element->amountElement()->getAttribute('data-bar'));
    }

    public function testElementOptionsWillBeProvidedToElements(): void
    {
        $this->element->setOptions([
            'currency_options' => ['label' => 'C'],
            'amount_options' => ['label' => 'Amt'],
        ]);
        self::assertSame('C', $this->element->currencyElement()->getLabel());
        self::assertSame('Amt', $this->element->amountElement()->getLabel());
    }
}
