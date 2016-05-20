# Changelog
All Notable changes to `ElementFinder` will be documented in this file

## Version 0.1.0-alpha.2
- Improved `ElementFinder::KeyValue()`.
- Deprecated `ElementFinder::attribute()`. Use `ElementFinder::value()` instead.
- Skip `XpathExpression` creation. By default use Xpath. Use `CssExpression` only when needed.  

## Version 0.0.3
- Feature #4 Use `DOMAttr::nodeValue` instead of `DOMAttr::value`    
- BC #7 Refactor `Helper` class. Create `FormHelper`, `NodeHelper` and `StringHelper`    