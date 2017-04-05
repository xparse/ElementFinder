<?php

  declare(strict_types=1);

  namespace Xparse\ElementFinder\Helper;

  use Xparse\ElementFinder\ElementFinder;

  /**
   * @author Ivan Shcherbak <alotofall@gmail.com>
   */
  class FormHelper {
    /**
     * @var ElementFinder
     */
    private $page;

    /**
     * * @param ElementFinder $page
     */
    public function __construct(ElementFinder $page) {
      $this->page = $page;
    }

    /**
     * Get data from <form> element
     *
     * Form is get by $expression
     * Return key->value array where key is name of field
     *
     * @param string $expression css or xpath expression to form element
     * @return array
     * @throws \Exception
     */
    public function getFormData(string $expression) : array {

      $form = $this->page->object($expression, true)->getFirst();
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
        $name = $select->value('//select/@name')->getFirst();
        $options = [];

        foreach ($select->value('//option[@selected]/@value') as $option) {
          $options[] = $option;
        }
        if (count($options) !== 0) {
          $formData[$name] = implode(',', $options);
        }
      }

      return $formData;
    }
  }