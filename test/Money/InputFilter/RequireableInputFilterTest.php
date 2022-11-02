<?php

declare(strict_types=1);

namespace ACETest\Money\InputFilter;

use ACE\Money\InputFilter\RequireableInputFilter;
use ACETest\Money\TestCase;

class RequireableInputFilterTest extends TestCase
{
    public function testSetRequired(): void
    {
        $input = new RequireableInputFilter();
        self::assertTrue($input->isRequired());
        $input->setRequired(false);
        self::assertFalse($input->isRequired());
    }

    public function testRequiredInputsAreRequiredByDefault(): void
    {
        $input = new RequireableInputFilter();
        $input->add([
            'name' => 'test',
            'required' => true,
        ]);
        $input->setData([]);
        self::assertFalse($input->isValid());
        $input->setData(['test' => null]);
        self::assertFalse($input->isValid());
        $input->setData(['test' => 'Foo']);
        self::assertTrue($input->isValid());
    }

    public function testEntireInputFilterCanBeMadeOptional(): void
    {
        $input = new RequireableInputFilter();
        $input->add([
            'name' => 'test',
            'required' => true,
        ]);
        $input->setRequired(false);
        $input->setData([]);
        self::assertTrue($input->isValid());
        $input->setData(['test' => null]);
        self::assertTrue($input->isValid());
        $input->setData(['test' => 'Foo']);
        self::assertTrue($input->isValid());
    }

    public function testNestedInputIsRequiredByDefault(): void
    {
        $input = new RequireableInputFilter();
        $input->add([
            'name' => 'test',
            'required' => true,
        ]);
        $child = new RequireableInputFilter();
        $child->add([
            'name' => 'child',
            'required' => true,
        ]);
        $input->add($child, 'nested');
        $input->setData(['test' => 'Foo']);
        self::assertFalse($input->isValid());
        $messages = $input->getMessages();
        self::assertArrayHasKey('nested', $messages);
        self::assertIsArray($messages['nested']);
        self::assertArrayHasKey('child', $messages['nested']);
    }

    public function testNestedInputCanBeOptional(): void
    {
        $input = new RequireableInputFilter();
        $input->add([
            'name' => 'test',
            'required' => true,
        ]);
        $child = new RequireableInputFilter();
        $child->add([
            'name' => 'child',
            'required' => true,
        ]);
        $child->setRequired(false);
        $input->add($child, 'nested');
        $input->setData(['test' => 'Foo']);
        self::assertTrue($input->isValid());
        $messages = $input->getMessages();
        self::assertArrayNotHasKey('nested', $messages);
    }

    public function testNestedFiltersAreValidatedIfNonEmptyEvenIfParentFilterIsOptional(): void
    {
        $input = new RequireableInputFilter();
        $input->setRequired(false);
        $input->add([
            'name' => 'test',
            'required' => true,
        ]);
        $child = new RequireableInputFilter();
        $child->add([
            'name' => 'child',
            'required' => true,
        ]);
        $input->add($child, 'nested');
        $input->setData([
            'test' => null,
            'nested' => ['child' => 'Foo'],
        ]);
        self::assertFalse($input->isValid());
        $messages = $input->getMessages();
        self::assertArrayNotHasKey('nested', $messages);
        self::assertArrayHasKey('test', $messages);
    }
}
