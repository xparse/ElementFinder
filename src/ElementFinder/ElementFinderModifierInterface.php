<?php

  declare(strict_types=1);

  namespace Xparse\ElementFinder\ElementFinder;

/**
   * @author Ivan Scherbak <alotofall@gmail.com>
   */
  interface ElementFinderModifierInterface
  {

      /**
       * @param \DOMNodeList $nodeList
       * @return void
       */
      public function modify(\DOMNodeList $nodeList);
  }
