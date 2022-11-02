<?php

declare(strict_types=1);

namespace ACE\Money\Container;

use ACE\Money\Hydrator\MoneyHydrator;
use Money\Currencies;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Parser\DecimalMoneyParser;
use Psr\Container\ContainerInterface;

class MoneyHydratorFactory
{
    public function __invoke(ContainerInterface $container): MoneyHydrator
    {
        $currencies = $container->get(Currencies::class);

        return new MoneyHydrator(
            new DecimalMoneyFormatter($currencies),
            new DecimalMoneyParser($currencies),
        );
    }
}
