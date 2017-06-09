<?php

/**
 * User: joachimdoerr
 * Date: 22.12.16
 * Time: 17:56
 */
class StoreProductFormHelper
{
    public static function addPriceInputElement(rex_form $form, array $item, $id = null)
    {
        $element = $form->addTextField($item['name']);

        // TODO default währung replace for title
        $element->setLabel(sprintf(StoreHelper::getLabel($item), '€', 'inkl. MwSt.'));

        if (array_key_exists('style', $item)) {
            $element->setAttribute('style', $item['style']);
        }

        return $element;
    }
}