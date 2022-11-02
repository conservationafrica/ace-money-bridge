<?php

declare(strict_types=1);

namespace ACE\Money\Container;

use ACE\Money\Form\Element\CurrencySelect;
use Money\Currencies;
use Psr\Container\ContainerInterface;

class CurrencySelectFactory
{
    public function __invoke(ContainerInterface $container): CurrencySelect
    {
        return new CurrencySelect(
            $container->get(Currencies::class),
        );
    }
}
