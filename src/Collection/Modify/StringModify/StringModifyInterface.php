<?php

  declare(strict_types=1);

  namespace Xparse\ElementFinder\Collection\Modify\StringModify;

  /**
   *
   */
  interface StringModifyInterface {

    /**
     * @param string $input
     * @return string
     */
    public function modify(string $input): string;

  }