# Changelog
All Notable changes to `ElementFinder` will be documented in this file

## 0.1.0-alpha.6 [Unreleased]

### Deprecated

### Removed
 - #53 Remove `ArrayAccess` interface from the `StringCollection`, `ObjectCollection` and `ElementCollection`
 - #52 Replace `Iterator`  with `IteratorAggregate` interface inside `StringCollection`, `ObjectCollection` and `ElementCollection`

### Added
 - #50 Add `StringCollection::unique` function
  
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