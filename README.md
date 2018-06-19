# ElementFinder

[![Latest Version](https://img.shields.io/packagist/v/xparse/element-finder.svg?style=flat-square)](https://packagist.org/packages/xparse/element-finder)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/xparse/ElementFinder/master.svg?style=flat-square)](https://travis-ci.org/xparse/ElementFinder)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/xparse/ElementFinder.svg?style=flat-square)](https://scrutinizer-ci.com/g/xparse/ElementFinder/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/xparse/ElementFinder.svg?style=flat-square)](https://scrutinizer-ci.com/g/xparse/ElementFinder)
[![Total Downloads](https://img.shields.io/packagist/dt/xparse/element-finder.svg?style=flat-square)](https://packagist.org/packages/xparse/element-finder)

Extract data from html with elegant xpath/css expressions and prepare data with regexp in single line.  

## Install

Via Composer

``` bash
$ composer require xparse/element-finder
```

## Usage

``` php
  $page = new ElementFinder($html);
  $title = $page->value('//title')->first();  
  echo $title;  
```

## Advanced usage with regexp


``` php
$page = new \Xparse\ElementFinder\ElementFinder('<html>
 
<div class="tels">
    044-12-12,
    258-16-16
</div>

<div class="tels">
    (148) 04-55-16
</div>
 
 </html>');

  $tels = $page->value('//*[@class="tels"]')->split('!,!')->replace("![^0-9]!");
  print_r($tels);
  
  /* 
    [0] => 0441212
    [1] => 2581616
    [2] => 148045516
  */
  

```

## Css selectors
Read this document. [Using css selectors](doc/using_css_selectors.md).

## Testing

``` bash
  ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/xparse/ElementFinder/blob/master/CONTRIBUTING.md) for details.

## Credits

- [funivan](https://github.com/funivan)
- [All Contributors](https://github.com/xparse/ElementFinder/contributors)

## Xpath info
- [XPath/CSS Equivalents](https://en.wikibooks.org/wiki/XPath/CSS_Equivalents)
- [Choose between XPath and jQuery with an XPath-jQuery phrase book](http://www.ibm.com/developerworks/library/x-xpathjquery/)
- [XPath and CSS Selectors](http://ejohn.org/blog/xpath-css-selectors/)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
