<?

  declare(strict_types=1);

  namespace Xparse\ElementFinder\ElementFinder;

  interface ElementFinderModifierInterface {

    /**
     * @param \DOMNodeList $nodeList
     * @return void
     */
    public function modify(\DOMNodeList $nodeList);

  }