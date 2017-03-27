<?php

  declare(strict_types=1);

  namespace Xparse\ElementFinder\Helper;

  use Xparse\ElementFinder\ElementFinder;

  /**
   * @author Ivan Shcherbak <alotofall@gmail.com>
   */
  class FormHelper {

    /**
     * Get data from <form> element
     *
     * Form is get by $xpath
     * Return key->value array where key is name of field
     *
     * @param ElementFinder $page
     * @param string $xpath xpath to form
     * @return array
     * @throws \Exception
     */
    public static function getDefaultFormData(ElementFinder $page, string $xpath) : array {

      $form = $page->object($xpath, true)->getFirst();
      if ($form === null) {
        throw new \Exception('Cant find form. Possible invalid xpath ');
      }

      $formData = [];
      # textarea
      foreach ($form->element('//textarea') as $textArea) {
        $formData[$textArea->getAttribute('name')] = $textArea->nodeValue;
      }

      # radio and checkboxes
      foreach ($form->element('//input[@checked]') as $textArea) {
        $formData[$textArea->getAttribute('name')] = $textArea->getAttribute('value');
      }

      # hidden, text, submit
      $hiddenAndTextElements = $form->element('//input[@type="hidden" or @type="text" or @type="submit" or not(@type)]');
      foreach ($hiddenAndTextElements as $element) {
        $formData[$element->getAttribute('name')] = $element->getAttribute('value');
      }

      # select
      $selectItems = $form->object('//select', true);
      foreach ($selectItems as $select) {
        $name = $select->value('//select/@name')->item(0);
        $option = $select->value('//option[@selected]');

        if ($option->getFirst() === null) {
          $option = $select->value('//option[1]');
        }
        $formData[$name] = $option->getFirst();
      }

      return $formData;
    }
  }