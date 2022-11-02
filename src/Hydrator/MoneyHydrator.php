<?php

declare(strict_types=1);

namespace ACE\Money\Hydrator;

use ACE\Money\Exception\InvalidArgumentException;
use Laminas\Hydrator\HydratorInterface;
use Money\Currency;
use Money\Money;
use Money\MoneyFormatter;
use Money\MoneyParser;

use function array_key_exists;
use function get_class;
use function gettype;
use function is_object;
use function sprintf;

class MoneyHydrator implements HydratorInterface
{
    /** @var MoneyFormatter */
    private $formatter;

    /** @var MoneyParser */
    private $parser;

    public function __construct(MoneyFormatter $formatter, MoneyParser $parser)
    {
        $this->formatter = $formatter;
        $this->parser = $parser;
    }

    /** @inheritDoc */
    public function extract($object): array
    {
        $money = $this->assertMoneyInstance($object);

        return [
            'currency' => $money->getCurrency()->getCode(),
            'amount' => $this->formatter->format($money),
        ];
    }

    /** @inheritDoc */
    public function hydrate(array $data, $object = null)
    {
        $this->assertExpectedArrayStructure($data);

        return $this->parser->parse($data['amount'], new Currency($data['currency']));
    }

    /** @param mixed $object */
    private function assertMoneyInstance($object): Money
    {
        if (! $object instanceof Money) {
            throw new InvalidArgumentException(sprintf(
                'Expected an instance of %s but received %s',
                Money::class,
                is_object($object) ? get_class($object) : gettype($object),
            ));
        }

        return $object;
    }

    /** @param mixed[] $data */
    private function assertExpectedArrayStructure(array $data): void
    {
        if (! array_key_exists('currency', $data)) {
            throw new InvalidArgumentException(
                'Expected the array key \'currency\' to be present in the input array',
            );
        }

        if (! array_key_exists('amount', $data)) {
            throw new InvalidArgumentException(
                'Expected the array key \'amount\' to be present in the input array',
            );
        }
    }
}
