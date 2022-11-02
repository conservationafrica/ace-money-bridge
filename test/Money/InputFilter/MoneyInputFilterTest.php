<?php

declare(strict_types=1);

namespace ACETest\Money\InputFilter;

use ACE\Money\Hydrator\MoneyHydrator;
use ACE\Money\InputFilter\MoneyInputFilter;
use ACE\Money\Validator\CurrencyValidator;
use ACETest\Money\TestCase;
use Laminas\Hydrator\HydratorPluginManager;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterPluginManager;
use Laminas\Validator\Regex;

use function json_encode;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;

class MoneyInputFilterTest extends TestCase
{
    /** @var MoneyInputFilter */
    private $filter;

    /** @var MoneyHydrator */
    private $hydrator;

    public function setUp(): void
    {
        parent::setUp();
        $container = $this->getContainer();
        $filters = $container->get(InputFilterPluginManager::class);
        $this->filter = $filters->get(MoneyInputFilter::class);
        $hydrators = $container->get(HydratorPluginManager::class);
        $this->hydrator = $hydrators->get(MoneyHydrator::class);
    }

    /** @return list<array{0: string, 1: int|float|string, 2: int}> */
    public function validData(): iterable
    {
        return [
            // Various valid number representations
            ['GBP', '1000.25', 100025],
            ['GBP', '0', 0],
            ['GBP', '0.00', 0],
            ['GBP', '0.01', 1],
            ['GBP', '100', 10000],
            ['GBP', '-0.01', -1],
            ['GBP', '-1', -100],
            // Currency Codes should be upper-cased and trimmed
            ['gbp', '1', 100],
            [' gbp ', '1', 100],
            // Numbers should be trimmed
            ['GBP', ' 1 ', 100],
            // Integers and floats should be acceptable
            ['GBP', 1, 100],
            ['GBP', 1.01, 101],
            ['GBP', -1, -100],
            ['GBP', -1.1, -110],
        ];
    }

    /**
     * @param int|string|float $amount
     *
     * @dataProvider validData
     */
    public function testValidValues(string $code, $amount, int $expectedAmount): void
    {
        $this->filter->setData([
            'currency' => $code,
            'amount' => $amount,
        ]);
        self::assertTrue($this->filter->isValid());
        $money = $this->hydrator->hydrate($this->filter->getValues(), null);
        self::assertEquals($expectedAmount, $money->getAmount());
    }

    /** @return list<array{0: mixed, 1: string, 2: string, 3: string}> */
    public function invalidValues(): iterable
    {
        return [
            ['ZZZ', '1', 'currency', CurrencyValidator::CODE_NOT_ACCEPTABLE],
            [['GBP'], '1', 'currency', CurrencyValidator::INVALID_TYPE],
            [1, '1', 'currency', CurrencyValidator::INVALID_CODE],
            ['ABCD', '1', 'currency', CurrencyValidator::INVALID_CODE],
            ['abcd', '1', 'currency', CurrencyValidator::INVALID_CODE],
            ['GBP', '1,000', 'amount', Regex::NOT_MATCH],
            ['GBP', '+1', 'amount', Regex::NOT_MATCH],
            ['GBP', '1,00', 'amount', Regex::NOT_MATCH],
        ];
    }

    /**
     * @param mixed $code
     *
     * @dataProvider invalidValues
     */
    public function testInvalidValues($code, string $amount, string $elementName, string $errorKey): void
    {
        $this->filter->setData([
            'currency' => $code,
            'amount' => $amount,
        ]);
        self::assertFalse($this->filter->isValid());
        $messages = $this->filter->getMessages();
        self::assertArrayHasKey($elementName, $messages);
        self::assertIsArray($messages[$elementName]);
        self::assertArrayHasKey($errorKey, $messages[$elementName], json_encode($messages, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    }

    private function generateParentInputFilter(): InputFilter
    {
        $parentInputFilter = new InputFilter();
        $parentInputFilter->add([
            'name' => 'test',
            'required' => true,
        ]);
        $parentInputFilter->add($this->filter, 'money');

        return $parentInputFilter;
    }

    public function testNestedMoneyFilterIsRequiredByDefault(): void
    {
        $parentInputFilter = $this->generateParentInputFilter();
        self::assertTrue($this->filter->isRequired());
        $parentInputFilter->setData([
            'test' => null,
            'money' => [
                'currency' => null,
                'amount' => null,
            ],
        ]);
        self::assertFalse($parentInputFilter->isValid());
        $messages = $parentInputFilter->getMessages();
        self::assertArrayHasKey('test', $messages);
        self::assertArrayHasKey('money', $messages);
        self::assertIsArray($messages['money']);
        self::assertArrayHasKey('currency', $messages['money']);
        self::assertArrayHasKey('amount', $messages['money']);
    }

    public function testNestedMoneyFilterCanBeOptional(): void
    {
        $parentInputFilter = $this->generateParentInputFilter();
        $this->filter->setRequired(false);
        self::assertFalse($this->filter->isRequired());
        $parentInputFilter->setData([
            'test' => 'Foo',
            'money' => [
                'currency' => null,
                'amount' => null,
            ],
        ]);
        self::assertTrue($parentInputFilter->isValid());
        $messages = $parentInputFilter->getMessages();
        self::assertArrayNotHasKey('test', $messages);
        self::assertArrayNotHasKey('money', $messages);
    }
}
