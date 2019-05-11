<?php

declare(strict_types=1);

namespace Xparse\ElementFinder\Helper;

use Xparse\ElementFinder\ElementFinderInterface;

/**
 * @author Ivan Shcherbak <alotofall@gmail.com>
 */
class FormHelper
{

    /**
     * @var ElementFinderInterface
     */
    private $page;


    public function __construct(ElementFinderInterface $page)
    {
        $this->page = $page;
    }


    /**
     * Get data from <form> element
     *
     * Form is get by $formExpression
     * Return key->value array where key is name of field
     *
     * @param string $formExpression css or xpath expression to form element
     * @throws \Exception
     */
    final public function getFormData(string $formExpression): array
    {
        $form = $this->page->object($formExpression, true)->first();
        if ($form === null) {
            throw new \InvalidArgumentException('Cant find form. Possible invalid expression ');
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

        # selects
        foreach ($form->object('//select[not(@multiple)]', true) as $select) {
            $name = $select->value('//select/@name')->first();
            if ($name === null) {
                continue;
            }
            $formData[$name] = $select->value('//option[@selected]/@value')->first();
        }

        # multiple selects
        foreach ($form->object('//select[@multiple]', true) as $multipleSelect) {
            $name = $multipleSelect->value('//select/@name')->first();
            if ($name === null) {
                continue;
            }
            $options = $multipleSelect->value('//option[@selected]/@value');
            if (preg_match('!\[\]$!', $name)) {
                $name = rtrim($name, '[]');
                $formData[$name] = $options->all();
            } else {
                $formData[$name] = $options->last();
            }
        }
        return $formData;
    }
}
