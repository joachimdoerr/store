<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class StoreZonesFormHelper
{
    /**
     * @param rex_form $form
     * @param array $item
     * @param null $id
     * @return mixed|rex_form_select_element
     * @author Joachim Doerr
     */
    public static function addZonesSelectElement(rex_form $form, array $item, $id = null)
    {
        $item['query'] = "SELECT name_en_gb as name, id FROM `rex_store_zones`";
        return StoreFormHelper::addSelectField($form, $item);
    }

    /**
     * @param rex_form $form
     * @param array $item
     * @param null $id
     * @return mixed|rex_form_select_element
     * @author Joachim Doerr
     */
    public static function addCountriesSelectElement(rex_form $form, array $item, $id = null)
    {
        $item['query'] = "SELECT name_en_gb as name, id FROM `rex_store_countries`";
        return StoreFormHelper::addSelectField($form, $item);
    }

    /**
     * @param rex_form $form
     * @param array $item
     * @param null $id
     * @return mixed|rex_form_element
     * @author Joachim Doerr
     */
    public static function addPriorityElement(rex_form $form, array $item, $id = null)
    {
        // TODO add text input nummerisch 0 as default by add

        $element = $form->addHiddenField($item['name'],0);
        return $element;

//        $element = $form->addPrioField($item['name']);
//        $element->setLabelField('name_1');
//        $element->setAttribute('class', 'selectpicker form-control');
    }
}