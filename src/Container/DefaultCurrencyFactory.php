<?php

declare(strict_types=1);

namespace ACE\Money\Container;

use ACE\Money\Exception\ConfigurationException;
use Money\Currency;
use Psr\Container\ContainerInterface;

use function is_string;

class DefaultCurrencyFactory
{
    public function __invoke(ContainerInterface $container): Currency
    {
        $config = $container->get('config');
        $code = $config['defaultCurrencyCode'] ?? null;
        if (! is_string($code)) {
            throw new ConfigurationException(
                'Configuration should have a key "defaultCurrencyCode" that contains a string representing ' .
                'the default currency code to use when one is otherwise unavailable',
            );
        }

        return new Currency($config['defaultCurrencyCode']);
    }
}
