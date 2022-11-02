<?php

declare(strict_types=1);

namespace ACE\Money\Container;

use ACE\Money\Validator\CurrencyValidator;
use ACE\Money\Validator\MoneyValidator;
use Laminas\Validator\ValidatorPluginManager;
use Psr\Container\ContainerInterface;

class MoneyValidatorFactory
{
    public function __invoke(ContainerInterface $container): MoneyValidator
    {
        $validators = $container->get(ValidatorPluginManager::class);

        return new MoneyValidator(
            $validators->get(CurrencyValidator::class),
        );
    }
}
