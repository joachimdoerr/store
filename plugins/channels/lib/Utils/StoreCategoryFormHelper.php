<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class StoreCategoryFormHelper
{
    /**
     * @param rex_form $form
     * @param array $item
     * @param null $id
     * @return mixed|rex_form_select_element
     * @author Joachim Doerr
     */
    public static function addParentSelectElement(rex_form $form, array $item, $id = null)
    {
        $channel = rex_request::request('channel', 'int');

        // TODO excpetion channel not given

        if (is_null($id) || $id == 0) {
            $mode = 'edit';
        }

//         $query_old = 'SELECT name_' . rex_clang::getCurrentId() . ' as name, id, parent as parent_id FROM rex_store_categories ORDER BY prio, name';
        $query = "
SELECT name_".rex_clang::getCurrentId()." as name, id, parent as parent_id
FROM `".StoreChannelsActions::CATEGORIES_TABLE."`
WHERE FIND_IN_SET(`id`, (
SELECT GROUP_CONCAT(Level SEPARATOR ',') FROM (
   SELECT @Ids := (
       SELECT GROUP_CONCAT(`ID` SEPARATOR ',')
       FROM `".StoreChannelsActions::CATEGORIES_TABLE."`
       WHERE FIND_IN_SET(`parent`, @Ids)
   ) Level
   FROM `".StoreChannelsActions::CATEGORIES_TABLE."`
   JOIN (SELECT @Ids := $channel) temp1
   WHERE FIND_IN_SET(`parent`, @Ids)
) temp2
)) ORDER by parent,prio;
    ";

//        echo '<pre>';
////        print_r($query);
//        print_r($item);
//        echo '</pre>';

        $element = $form->addSelectField($item['name']);
        $select = $element->getSelect();

        $sql = rex_sql::factory();
        $sql->setQuery($query);

        $array = array_merge([array('name'=>'---', 'id'=>$channel, 'parent_id'=>0)],$sql->getArray());
        foreach ($array as $value) {
            if ($id != $value['id']) {
                $select->addOption($value['name'], $value['id'], $value['id'], $value['parent_id']);
            }
        }

        $element->setLabel(StoreHelper::getLabel($item));

        if (array_key_exists('style', $item)) {
            $element->setAttribute('style', $item['style']);
        }

        return $element;
    }

    /**
     * @param rex_form $form
     * @param array $item
     * @param null $id
     * @return mixed|rex_form_select_element
     * @author Joachim Doerr
     */
    public static function addCategoriesSelectElement(rex_form $form, array $item, $id = null)
    {
        $query = "SELECT name_".rex_clang::getCurrentId()." as name, id, parent as parent_id FROM `".StoreChannelsActions::CATEGORIES_TABLE."`";

        $sql = rex_sql::factory();
        $sql->setQuery($query);

        $element = $form->addSelectField($item['name']);
        $select = $element->getSelect();
        $select->setMultiple(true);

        if ($sql->getRows() < 10) {
            $select->setSize($sql->getRows());
        }
        $select->setSize(10);

        while($sql->hasNext()) {
            $select->addOption($sql->getValue('name'), $sql->getValue('id'), $sql->getValue('id'), $sql->getValue('parent_id'));
            $sql->next();
        }

        if ($form->isEditMode()) {
            $select->setSelected(explode(',', str_replace(array('[',']', '"'), '', $element->getValue())));
        }

        $element->setLabel(StoreHelper::getLabel($item));

        return $element;
    }

    public static function addPriorityElement(rex_form $form, array $item, $id = null)
    {
        echo 'PRIO TODO in StoreCategoryFormHelper::addPriorityElement';
//        echo '<pre>';
//        print_r($item);
//        echo '</pre>';
//
//        $element = $form->addPrioField($item['name'], 'name_1');
//        $element->setLabelField('name_1');
    }
}