<?php

use Basecondition\Utils\ViewHelper;

/**
 * User: joachimdoerr
 * Date: 22.12.16
 * Time: 17:56
 */
class StoreProductFormHelper
{
    /**
     * @param rex_form $form
     * @param array $item
     * @param null $id
     * @param null $tableBaseName
     * @return rex_form_element
     * @author Joachim Doerr
     */
    public static function addPriceInputElement(rex_form $form, array $item, $id = null, $tableBaseName = null)
    {
        $element = $form->addTextField($item['name']);

        // TODO default währung replace for title
        $element->setLabel(sprintf(ViewHelper::getLabel($item, 'label', $tableBaseName), '€', 'inkl. MwSt.'));

        if (array_key_exists('style', $item)) {
            $element->setAttribute('style', $item['style']);
        }

        return $element;
    }
}