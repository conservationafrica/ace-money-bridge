<?php

declare(strict_types=1);

namespace ACE\Money\Container;

use ACE\Money\Form\Element\MoneyElement;
use ACE\Money\Hydrator\MoneyHydrator;
use Laminas\Hydrator\HydratorPluginManager;
use Psr\Container\ContainerInterface;

class MoneyElementFactory
{
    public function __invoke(ContainerInterface $container): MoneyElement
    {
        $hydrators = $container->get(HydratorPluginManager::class);

        return new MoneyElement(
            $hydrators->get(MoneyHydrator::class),
        );
    }
}
