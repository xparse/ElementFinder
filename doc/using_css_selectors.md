# Using CSS selectors
Xpath is very powerful query language. But sometimes, you do not need this power. You need just to grab some page in simple way - using css selectors.
Css selectors are widely used. They are simple.


You need additional library called `xparse/css-expression-translator`

Install it via composer:
```sh
composer require xparse/css-expression-translator
```

Configure element finder
```php
  $finder->setExpressionTranslator(new CssExpressionTranslator());
```

## Example
Here is full working example:
```php

  require 'vendor/autoload.php';

  use Xparse\CssExpressionTranslator\CssExpressionTranslator;
  use Xparse\ElementFinder\ElementFinder;


  $finder = new ElementFinder('<div>
      <a href="#page">123</a>
      <a href="#second" class="test">321<span>ad</span></a>
</div>', ElementFinder::DOCUMENT_HTML, new CssExpressionTranslator());
  

  # 321<span>ad</span>
  echo $finder->content('a.test')->first();
```  

## How it works?
This library build on top of the `symfony/css-selector` [https://github.com/symfony/css-selector](https://github.com/symfony/css-selector)

## How to select attributes with css?
Add space before attribute name.
```php
  $finder->attributes('a @href');
  $finder->attributes('a.test @class');
  
  // slect node text  
  $finder->value('a.test node()'); 
```
  
## Limits
There are some limits. 
- Xpath is more powerful than css.
- you cant select attributes with `or` operator
- fetch function result `a concat('text:', text())` 





