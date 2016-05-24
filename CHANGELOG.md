# Changelog
All Notable changes to `ElementFinder` will be documented in this file

## Unreleased [0.1.0-alpha.2 2016-05-26]
### Changed
- #18 Skip `XpathExpression` creation. Use `CssExpression` only when needed.
- #28 Method `ElementFinder::elements()` has been renamed to `ElementFinder::element()`, 
- #28 Method `ElementFinder::html()` has been renamed to `ElementFinder::content()`, 
- #28 Method `ElementFinder::query()` has been renamed to `ElementFinder::executeQuery()`,
- #28 Method `NodeHelper::getOuterHtml()` has been renamed to `NodeHelper::getOuterContent()`,
- #28 Method `NodeHelper::getInnerHtml()` has been renamed to `NodeHelper::getInnerContent()`,
- #28 Added `ElementFinder::query()` as an alias of `ElementFinder::node()`  
 
### Deprecated
- #29 `ElementFinder::getNodeItems()`
- #10 `ElementFinder::attribute()`. See `ElementFinder::value()`
- #14 Remove 3 parameter inside `ElementFinder::KeyValue()`

## Version 0.0.3
### Changed
- Feature #4 Use `DOMAttr::nodeValue` instead of `DOMAttr::value`
- BC #7 Refactor `Helper` class. Create `FormHelper`, `NodeHelper` and `StringHelper`