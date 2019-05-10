<?php

  declare(strict_types=1);

  namespace Xparse\ElementFinder\ElementFinder;

/**
   * @author Ivan Shcherbak <alotofall@gmail.com>
   */
  interface ElementFinderModifierInterface
  {

      /**
       * @return void
       */
      public function modify(\DOMNodeList $nodeList);
  }
