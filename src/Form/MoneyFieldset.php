<?php

declare(strict_types=1);

namespace ACE\Money\Form;

use ACE\Money\Hydrator\MoneyHydrator;
use Laminas\Form\ElementInterface;
use Laminas\Form\Fieldset;
use Money\Currency;
use Money\Money;

use function is_iterable;

class MoneyFieldset extends Fieldset
{
    public function __construct(
        ElementInterface $currencyElement,
        ElementInterface $amountElement,
        MoneyHydrator $hydrator,
        Currency $defaultCurrency
    ) {
        parent::__construct();

        $currencyElement->setName('currency');
        $this->add($currencyElement);
        $amountElement->setName('amount');
        $this->add($amountElement);
        $this->setHydrator($hydrator);
        $this->setAllowedObjectBindingClass(Money::class);
        // If an initial object is not set, hydration will not work and an array will be returned in getData()
        $this->setObject(
            new Money(0, $defaultCurrency),
        );
        $this->populateValues([
            'currency' => $defaultCurrency->getCode(),
        ]);
    }

    /** @inheritDoc */
    public function setOptions($options): self
    {
        parent::setOptions($options);

        $currencyOptions = $this->getOption('currency')['options'] ?? null;
        if (is_iterable($currencyOptions)) {
            $this->currencyElement()->setOptions($currencyOptions);
        }

        $currencyAttributes = $this->getOption('currency')['attributes'] ?? null;
        if (is_iterable($currencyAttributes)) {
            $this->currencyElement()->setAttributes($currencyAttributes);
        }

        $amountOptions = $this->getOption('amount')['options'] ?? null;
        if (is_iterable($amountOptions)) {
            $this->amountElement()->setOptions($amountOptions);
        }

        $amountAttributes = $this->getOption('amount')['attributes'] ?? null;
        if (is_iterable($amountAttributes)) {
            $this->amountElement()->setAttributes($amountAttributes);
        }

        return $this;
    }

    public function currencyElement(): ElementInterface
    {
        return $this->get('currency');
    }

    public function amountElement(): ElementInterface
    {
        return $this->get('amount');
    }
}
