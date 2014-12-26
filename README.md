# ElementFinder

[![Latest Version](https://img.shields.io/packagist/v/xparse/element-finder.svg?style=flat-square)](https://packagist.org/packages/xparse/element-finder)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/xparse/ElementFinder/master.svg?style=flat-square)](https://travis-ci.org/xparse/ElementFinder)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/xparse/ElementFinder.svg?style=flat-square)](https://scrutinizer-ci.com/g/xparse/ElementFinder/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/xparse/ElementFinder.svg?style=flat-square)](https://scrutinizer-ci.com/g/xparse/ElementFinder)
[![Total Downloads](https://img.shields.io/packagist/dt/xparse/element-finder.svg?style=flat-square)](https://packagist.org/packages/xparse/element-finder)

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what
PSRs you support to avoid any confusion with users and contributors.

## Install

Via Composer

``` bash
$ composer require xparse/element-finder
```

## Usage

``` php
  $page = new ElementFinder();
  $page->load($html)
  $title = $page->value('//title')->getFirst();  
  echo $title;  
```

## Testing

``` bash
  ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/xparse/ElementFinder/blob/master/CONTRIBUTING.md) for details.

## Credits

- [funivan](https://github.com/funivan)
- [All Contributors](https://github.com/xparse/ElementFinder/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
