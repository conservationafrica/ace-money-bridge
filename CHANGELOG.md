# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.4.1 - TBD

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.3.0 - 2021-09-08

### Added

- Nothing.

### Changed

- Allows PHP 8 in composer.json.
- Allows laminas-form ^3

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.2.1 - 2021-04-07

### Added

- Nothing.

### Changed

- Relaxed composer constraints allowing laminas hydrator v4 and psr/container v2.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.2.0 - 2021-03-22

### Added

- Nothing.

### Changed

- Bumps `laminas/laminas-form` to 2.16.x which happily introduces the form element manager as a concrete class deprecating the old polyfills.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.1.0 - 2020-12-31

### Added

- Nothing.

### Changed

- Updated PHPUnit configuration and removed usage of Prophecy in favour of PHPUnit mocks.
- Added Doctrine coding standard and updated code style to suit
- Miscellaneous house-keeping

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Passing deprecated string argument to money parser's parse method when it should be a currency instance _(In the money hydrator)_.

## 1.0.0 - 2020-03-04

### Added

- Nothing

### Changed

- [#6](https://github.com/gsteel/ace-money-bridge/pull/6) Upgrades dependencies and migrates to Laminas from Zend
    - Bumped minimum PHP Version to 7.3
    - Upgraded to Laminas\Hydrator 3.x
    - Upgrade PHPUnit to 9.x
    - Migrate to Laminas Equivalents from Zend
    - Declare other hidden dependencies in composer.json

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.6.0 - 2019-12-23

### Added

- [#5](https://github.com/gsteel/ace-money-bridge/pull/5) Adds new methods to the Money element that allow you to
 provide element options and attributes to the composed currency and amount elements

### Changed

- [#5](https://github.com/gsteel/ace-money-bridge/pull/5) The default money input specification adds the required
 attribute to the form elements as per it's invoke argument along with a default placeholder value.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.5.0 - 2019-12-16

### Added

- New filter that hydrates expected array input to a money instance if possible
- New validator that validates array input representing money and currency
- New composite form element that can be used instead of the fieldset

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.4.0 - 2019-10-17

### Added

- [#4](https://github.com/gsteel/ace-money-bridge/pull/4) Adds methods to `MoneyFieldset` for retrieving child elements
and makes it possible to configure the currency and amount element options and attributes _(Label for example)_ by
 providing additional options in the option keys 'amount' and 'currency'.

### Changed

- [#3](https://github.com/gsteel/ace-money-bridge/pull/3) Populates the `MoneyFieldset` with default currency.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.3.0 - 2019-10-08

### Added

- [#2](https://github.com/gsteel/ace-money-bridge/pull/2) Adds `RequireableInputFilter` enabling an entire input filter
to be optional. Input filters that are not `required` will be considered valid if all of the composed inputs are empty.
Any non empty inputs will trigger normal validation.

### Changed

- [#2](https://github.com/gsteel/ace-money-bridge/pull/2) Alters the money input filter to extend `RequireableInputFilter`
so that other input filters composing the `MoneyInputFilter` can choose to make the monetary value optional.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.2.0 - 2019-06-07

### Added

- [#1](https://github.com/gsteel/ace-money-bridge/pull/1) Add Filter for Converting Currency Codes into Currency Instances

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.1.0 - 2019-06-05

### Added

- Form Elements:
  - Currency as a text input
  - Currency as a select element
  - Amount as a Number element
  - A Money Fieldset that combines Currency and Amount elements that can be re-used in Forms.
- A Hydrator that will extract a Money instance to an array containing `currency` and `amount` keys and will hydrate
 an array in the same format to a Money instance. In each direction, the amount will be converted between an integer
 and a float and vice-versa
- An Input filter that can validate an array in the format expected by the hydrator
- A Currency validator that verifies currency code strings are sane and ensures they are permitted according to a
 pre-defined list of currencies.
- A ConfigProvider that wires up the form elements, hydrators, validators etc to the relevant plugin managers such as
 FormElementManager etc.
- A Default Currency concept that is simply a string at configuration top-level used by a factory to return it as a
 currency instance. The factory is aliased to `\Money\Currency`
- A Default List of allowable currencies - A factory that by default returns all ISO currencies as a `\Money\Currencies`
 instance. The factory is also aliased to this interface.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
