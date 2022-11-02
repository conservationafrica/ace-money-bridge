<?php

declare(strict_types=1);

namespace ACETest\Money\Form;

use ACE\Money\Form\MoneyFieldset;
use ACETest\Money\BindableObject;
use ACETest\Money\TestCase;
use Laminas\Form\Form;
use Laminas\Hydrator\ClassMethodsHydrator;
use Money\Money;

use function assert;

class FormIntegrationTest extends TestCase
{
    public function testProgrammaticFormCreation(): void
    {
        $container = $this->getContainer();
        $forms = $container->get('FormElementManager');

        $form = $forms->get(Form::class);
        assert($form instanceof Form);
        $form->setHydrator(new ClassMethodsHydrator());
        $form->add([
            'name' => 'amount',
            'type' => MoneyFieldset::class,
        ]);
        $object = new BindableObject();
        $form->bind($object);
        $form->setData([
            'amount' => [
                'currency' => 'GBP',
                'amount' => 1,
            ],
        ]);
        self::assertTrue($form->isValid());
        $money = $object->getAmount();
        self::assertInstanceOf(Money::class, $money);
        self::assertEquals(100, $money->getAmount());
    }

    public function testElementOptionsAndAttributesAreProvidedToIndividualElements(): void
    {
        $container = $this->getContainer();
        $forms = $container->get('FormElementManager');

        $form = $forms->get(Form::class);
        assert($form instanceof Form);
        $form->add([
            'name' => 'money',
            'type' => MoneyFieldset::class,
            'options' => [
                'currency' => [
                    'options' => ['label' => 'Currency Label'],
                    'attributes' => ['class' => 'c'],
                ],
                'amount' => [
                    'options' => ['label' => 'Amount Label'],
                    'attributes' => ['class' => 'a'],
                ],
            ],
        ]);
        $fieldset = $form->get('money');
        assert($fieldset instanceof MoneyFieldset);
        self::assertInstanceOf(MoneyFieldset::class, $fieldset);
        self::assertSame('Currency Label', $fieldset->currencyElement()->getLabel());
        self::assertSame('Amount Label', $fieldset->amountElement()->getLabel());
        self::assertSame('c', $fieldset->currencyElement()->getAttribute('class'));
        self::assertSame('a', $fieldset->amountElement()->getAttribute('class'));
    }
}
