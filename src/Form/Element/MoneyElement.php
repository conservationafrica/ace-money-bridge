<?php

declare(strict_types=1);

namespace ACE\Money\Form\Element;

use ACE\Money\Form\Element\Currency as CurrencyInput;
use ACE\Money\Form\Element\Money as MoneyInput;
use ACE\Money\Hydrator\MoneyHydrator;
use ACE\Money\Input\MoneyInputSpec;
use Laminas\Form\Element;
use Laminas\Form\ElementPrepareAwareInterface;
use Laminas\Form\FormInterface;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Money\Money;
use Traversable;

use function is_array;
use function sprintf;
use function trim;

class MoneyElement extends Element implements InputProviderInterface, ElementPrepareAwareInterface
{
    /** @var MoneyInput */
    private $amount;

    /** @var CurrencyInput */
    private $currency;

    /** @var MoneyHydrator|null */
    private $hydrator;

    /** @inheritDoc */
    public function __construct(MoneyHydrator $hydrator, ?string $name = null, $options = [])
    {
        $this->amount = new MoneyInput();
        $this->currency = new CurrencyInput();
        $this->hydrator = $hydrator;

        parent::__construct($name, $options);
    }

    /** @inheritDoc */
    public function setValue($value)
    {
        if ($value instanceof Money) {
            $value = $this->hydrator->extract($value);
        }

        if (is_array($value) && isset($value['currency'], $value['amount'])) {
            $this->currency->setValue($value['currency']);
            $this->amount->setValue($value['amount']);
        }

        return $this;
    }

    /** @inheritDoc */
    public function getValue()
    {
        return trim(sprintf(
            '%s %s',
            $this->currency->getValue(),
            $this->amount->getValue(),
        ));
    }

    public function prepareElement(FormInterface $form): void
    {
        $name = $this->getName();
        $this->amount->setName($name . '[amount]');
        $this->currency->setName($name . '[currency]');
    }

    /** @inheritDoc */
    public function getInputSpecification(): array
    {
        $spec = (new MoneyInputSpec())($this->hasAttribute('required'));
        $spec['name'] = $this->getName();

        return $spec;
    }

    public function currencyElement(): CurrencyInput
    {
        return $this->currency;
    }

    public function amountElement(): MoneyInput
    {
        return $this->amount;
    }

    /** @inheritDoc */
    public function setOptions($options): self
    {
        parent::setOptions($options);

        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (isset($options['currency_options'])) {
            $this->setCurrencyOptions($options['currency_options']);
        }

        if (isset($options['currency_attributes'])) {
            $this->setCurrencyAttributes($options['currency_attributes']);
        }

        if (isset($options['amount_options'])) {
            $this->setAmountOptions($options['amount_options']);
        }

        if (isset($options['amount_attributes'])) {
            $this->setAmountAttributes($options['amount_attributes']);
        }

        return $this;
    }

    /**
     * Set attributes for the currency element
     *
     * @param mixed[] $currencyAttributes
     */
    public function setCurrencyAttributes(array $currencyAttributes): self
    {
        $this->currency->setAttributes($currencyAttributes);

        return $this;
    }

    /**
     * Set attributes for the amount element
     *
     * @param mixed[] $amountAttributes
     */
    public function setAmountAttributes(array $amountAttributes): self
    {
        $this->amount->setAttributes($amountAttributes);

        return $this;
    }

    /**
     * Set options for the currency element
     *
     * @param mixed[] $currencyOptions
     */
    public function setCurrencyOptions(array $currencyOptions): self
    {
        $this->currency->setOptions($currencyOptions);

        return $this;
    }

    /**
     * Set options for the amount element
     *
     * @param mixed[] $amountOptions
     */
    public function setAmountOptions(array $amountOptions): self
    {
        $this->amount->setOptions($amountOptions);

        return $this;
    }
}
