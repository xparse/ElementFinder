# Changelog
All Notable changes to `ElementFinder` will be documented in this file
## 0.5.0 [2019-05-14]
- Use `ElementFinderInterface` instead of `ElementFinder` class
- Move to php 7.1 

## 0.4.0 [2019-05-11]
- Remove ElementFinder\ElementFinderModifierInterface see DomNodeListAction\DomNodeListActionInterface
- Remove ElementFinder\RemoveElements see DomNodeListAction\RemoveNodes
- Remove deprecated class RegexHelper 
- Specify types
- Add final modifiers for all public methods
## 0.3.1 [2018-04-18]
- Add new method: `ElementFinder::modify`.
 
## 0.3.0 [2018-03-22]

### Changed  
- #84 `ElementFinder` become immutable 
- #84 method `ElementFinder::remove` return `new ElementFinder()`
- #84 method `ElementFinder::element` return copy of the element
- Make second argument required `\Xparse\ElementFinder\Collection\StringCollection::replace`
- #87 Remove exceptions from the constructor `ElementCollection`, `StringCollection`, `ObjectCollection`    

### Deprecated 
 - #95 deprecate internal method `\Xparse\ElementFinder\Helper\RegexHelper::match`
 
### Removed
- #86 Remove deprecated method `\Xparse\ElementFinder\ElementFinder::match`
- #86 Remove deprecated method `\Xparse\ElementFinder\ElementFinder::__toString`
- #86 Remove deprecated method `\Xparse\ElementFinder\Collection\StringCollection::getLast`
- #86 Remove deprecated method `\Xparse\ElementFinder\Collection\StringCollection::getFirst`
- #86 Remove deprecated method `\Xparse\ElementFinder\Collection\StringCollection::getItems`
- #86 Remove deprecated method `\Xparse\ElementFinder\Collection\StringCollection::walk`
- #86 Remove deprecated method `\Xparse\ElementFinder\Collection\ObjectCollection::getLast`
- #86 Remove deprecated method `\Xparse\ElementFinder\Collection\ObjectCollection::getFirst`
- #86 Remove deprecated method `\Xparse\ElementFinder\Collection\ObjectCollection::getItems`
- #86 Remove deprecated method `\Xparse\ElementFinder\Collection\ObjectCollection::walk`
- #86 Remove deprecated method `\Xparse\ElementFinder\Collection\ElementCollection::getLast`
- #86 Remove deprecated method `\Xparse\ElementFinder\Collection\ElementCollection::getFirst`
- #86 Remove deprecated method `\Xparse\ElementFinder\Collection\ElementCollection::getItems`
- #86 Remove deprecated method `\Xparse\ElementFinder\Collection\ElementCollection::walk`
- #86 Remove deprecated method ``

## 0.2.1 [2017-12-27]

### Deprecated 
- #98 Deprecate `ElementCollection::walk`
- #98 Deprecate `StringCollection::walk`
- #98 Deprecate `ObjectCollection::walk`
- #100 Deprecate `ElementCollection::getItems` see `ElementCollection::all` 
- #100 Deprecate `StringCollection::getItems` see `StringCollection::all`
- #100 Deprecate `ObjectCollection::getItems` see `ObjectCollection::all`
- #99 Deprecate `ElementCollection::getFirst` see `ElementCollection::first` 
- #99 Deprecate `StringCollection::getFirst` see `StringCollection::first` 
- #99 Deprecate `ObjectCollection::getFirst` see `ObjectCollection::first` 
- #99 Deprecate `ElementCollection::getLast` see `ElementCollection::last` 
- #99 Deprecate `StringCollection::getLast` see `StringCollection::last` 
- #99 Deprecate `ObjectCollection::getLast` see `ObjectCollection::last` 

### Changed  
- #92 Require second parameter `StringCollection::replace`
- #93 All public methods become final.
- #94 All protected methods become private


## 0.2.0 [2017-11-02]

### Deprecated  
- #85 Deprecate `ElementFinder::match`
- #88 Deprecate `ElementFinder::__toString`

### Removed
- #82 Remove method `ElementCollection::getAttributes`  
- #82 Remove method `ElementFinder::setExpressionTranslator()`
- #82 Remove method `ElementFinder::getExpressionTranslator()`
- #82 Remove method `ObjectCollection::append()`
- #82 Remove method `ElementFinder::replace()`
- #82 Remove method `ElementFinder::getType()`
- #82 Remove method `ElementFinder::getOptions()`
- #83 Remove method `ElementFinder::query`


## 0.1.0-alpha.7 [2017-08-21]
### Added
 - #81 Introduce new map method `StringCollection::map()`.
 - #48 Introduce new filter method `StringCollection::filter`.
 - #72 Add 3 argument to the `ElementFinder::__construct`. Now you can pass `ExpressionTranslatorInterface`

### Removed
 - #75 Remove  `options` parameter from the `ElementFinder::__construct`
 
### Deprecated
- #80 Deprecate `ElementCollection::getAttributes` 
- #72 Deprecate `ElementFinder::setExpressionTranslator()`
- #72 Deprecate `ElementFinder::getExpressionTranslator()`
- #66 Deprecate `ObjectCollection::append()`
- #70 Deprecate `ElementFinder::replace()`
- #77 Deprecate `ElementFinder::getType()`
- #74 Deprecate `ElementFinder::getOptions()`



## 0.1.0-alpha.6 [2017-08-16]

### Fixed
 - #62 `FormHelper` return value attribute in select elements.

### Deprecated
 - #58 Fire error if we try to store non string values inside `StringCollection`  
 - #57 Deprecate method `ElementFinder::node` use `ElementFinder::element` instead 

### Removed
 - #75 Method `ElementFinder::node`
 - #53 Remove `ArrayAccess` interface from the `StringCollection`, `ObjectCollection` and `ElementCollection`
 - #52 RegexReplace `Iterator`  with `IteratorAggregate` interface inside `StringCollection`, `ObjectCollection` and `ElementCollection`
 - #55 Remove (`StringCollection::prepend`,`StringCollection::addAfter`,`StringCollection::slice`,`StringCollection::extractItems`,`StringCollection::getNext`,`StringCollection::getPrevious`, `StringCollection::append`, `StringCollection::setItems`)  
 - #55 Remove (`ObjectCollection::prepend`,`ObjectCollection::addAfter`,`ObjectCollection::slice`,`ObjectCollection::extractItems`,`ObjectCollection::getNext`,`ObjectCollection::getPrevious`,`ObjectCollection::append`,`ObjectCollection::setItems`)  
 - #55 Remove (`ElementCollection::prepend`,`ElementCollection::addAfter`,`ElementCollection::slice`,`ElementCollection::extractItems`,`ElementCollection::getNext`,`ElementCollection::getPrevious`, `ElementCollection::append`, `ElementCollection::setItems`)  
 - #51 Remove (`ElementCollection::map`,`ObjectCollection::map`,`StringCollection::map`)  
 - Remove `StringCollection::item` use `StringCollection::get` instead  
 - Remove `ObjectCollection::item` use `ObjectCollection::get` instead  
 - Remove method `FormHelper::getDefaultFormData` use `FormHelper::getFormData` instead  
 - #59 Remove method `ObjectCollection::replace`   

### Changed
 - #54 Return new collection instead of modification (`StringCollection::replace`,`ObjectCollection::replace`)
   
### Added
 - #50 Add `StringCollection::unique` function
 - #56 Add `StringCollection::merge`, `ObjectCollection::merge` and `ElementCollection::merge` functions
 - #60 Add `StringCollection::add`,`StringCollection::get` methods
 - #60 Add `ObjectCollection::add`,`ObjectCollection::get` methods
 - #60 Add `ElementCollection::add`,`ElementCollection::get` methods
  
## 0.1.0-alpha.5 [2017-03-10]

### Added
- strict types declaration

### Changed
- all external collections where moved to appropriate ElementFinder collections

### Deprecated
- ArrayAccessible methods in Collections (offsetSet, offsetExists, offsetUnset, offsetGet)
- #49 deprecate `StringCollection::map`, `ObjectCollection::map`, `ElementCollection::map` use `walk` instead

### Removed
- fiv/collection package

## 0.1.0-alpha.3 [2016-06-02]

### Fixed
- #33 copy expression translator to child objects

### Removed
- method `ElementFinder::attribute()` has been removed
- method `ElementFinder::elements()` has been removed
- method `ElementFinder::getNodeItems()` has been removed
- method `ElementFinder::html()` has been removed
- method `NodeHelper::getInnerHtml()` has been removed
- method `NodeHelper::getOuterHtml()` has been removed
 

## 0.1.0-alpha.2 [2016-05-25]

### Added
- Added `ElementFinder::query()` as an alias of `ElementFinder::node()`
  
### Changed
- #18 Skip `XpathExpression` creation. Use `CssExpression` only when needed.
 
### Deprecated
- #29 `ElementFinder::getNodeItems()`
- #28 Method `ElementFinder::elements()` has been renamed to `ElementFinder::element()`
- #28 Method `ElementFinder::html()` has been renamed to `ElementFinder::content()`
- #28 Method `ElementFinder::query()` has been renamed to `ElementFinder::executeQuery()`
- #28 Method `NodeHelper::getOuterHtml()` has been renamed to `NodeHelper::getOuterContent()`
- #28 Method `NodeHelper::getInnerHtml()` has been renamed to `NodeHelper::getInnerContent()`
- #10 `ElementFinder::attribute()`. See `ElementFinder::value()`
- #14 Remove 3 parameter inside `ElementFinder::KeyValue()`

## Version 0.0.3

### Changed
- Feature #4 Use `DOMAttr::nodeValue` instead of `DOMAttr::value`
- BC #7 Refactor `Helper` class. Create `FormHelper`, `NodeHelper` and `StringHelper`