<?

  declare(strict_types=1);

  namespace Xparse\ElementFinder\ElementFinder;

  class RemoveElements implements ElementFinderModifierInterface {

    /**
     * @param \DOMNodeList $nodeList
     * @return void
     */
    public function modify(\DOMNodeList $nodeList) {
      foreach ($nodeList as $node) {
        if ($node instanceof \DOMAttr) {
          $node->ownerElement->removeAttribute($node->name);
        } else {
          $node->parentNode->removeChild($node);
        }
      }
    }
  }