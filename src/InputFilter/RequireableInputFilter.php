<?php

declare(strict_types=1);

namespace ACE\Money\InputFilter;

use Laminas\InputFilter\InputFilter;

use function array_filter;
use function in_array;
use function is_array;

class RequireableInputFilter extends InputFilter
{
    /** @var bool */
    private $required = true;

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    /**
     * {@inheritDoc}
     */
    public function isValid($context = null): bool
    {
        if (! $this->required && $this->isEmptyArray($this->getRawValues())) {
            return true;
        }

        return parent::isValid($context);
    }

    /** @param mixed[] $data */
    private function isEmptyArray(array $data): bool
    {
        $nonEmpty = array_filter($data);
        if (empty($nonEmpty)) {
            return true;
        }

        $isEmpty = [];
        foreach ($nonEmpty as $key => $child) {
            if (is_array($child)) {
                $isEmpty[$key] = $this->isEmptyArray($child);
                continue;
            }

            $isEmpty[$key] = empty($child);
        }

        return ! in_array(false, $isEmpty, true);
    }
}
