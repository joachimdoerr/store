<?php

use Basecondition\Utils\ViewHelper;

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
     * @param null $tableBaseName
     * @return mixed|rex_form_select_element
     * @throws rex_sql_exception
     * @author Joachim Doerr
     */
    public static function addParentSelectElement(rex_form $form, array $item, $id = null, $tableBaseName = null)
    {
        $channel = rex_request::request('channel', 'int');

        //$query = "
        //SELECT name_".rex_clang::getCurrentId()." as name, id, parent as parent_id
        //FROM `".rex::getTablePrefix() . StoreChannelsActions::CATEGORIES_TABLE."`
        //WHERE FIND_IN_SET(`id`, (
        //SELECT GROUP_CONCAT(Level SEPARATOR ',') FROM (
        //   SELECT @Ids := (
        //       SELECT GROUP_CONCAT(`ID` SEPARATOR ',')
        //       FROM `".rex::getTablePrefix() . StoreChannelsActions::CATEGORIES_TABLE."`
        //       WHERE FIND_IN_SET(`parent`, @Ids)
        //   ) Level
        //   FROM `".rex::getTablePrefix() . StoreChannelsActions::CATEGORIES_TABLE."`
        //   JOIN (SELECT @Ids := $channel) temp1
        //   WHERE FIND_IN_SET(`parent`, @Ids)
        //) temp2
        //)) ORDER by parent,prio;
        //";

        $k = 't';
        $currentLevel = null;
        $currentParent = null;
        $name = 'name_' . rex_clang::getCurrentId();

        $query = "
            SELECT  CONCAT(REPEAT('--', level - 1), ' ', $k.$name) AS name,
                    category_sys_connect_by_path('/', $k.id) AS path,
                    parent, level, $k.id
            FROM    (
                    SELECT  category_connect_by_parent_eq_prior_id_with_level(id, 10) AS id,
                            CAST(@level AS SIGNED) AS level
                    FROM    (
                            SELECT  @start_with := {$channel},
                                    @id := @start_with,
                                    @level := 0
                            ) vars, ".rex::getTablePrefix() . StoreChannelsActions::CATEGORIES_TABLE."
                    WHERE   @id IS NOT NULL
                    ) ho
            JOIN    ".rex::getTablePrefix() . StoreChannelsActions::CATEGORIES_TABLE." $k
            ON      $k.id = ho.id
            ORDER BY path
        ";

        $element = $form->addSelectField($item['name']);
        $select = $element->getSelect();

        $sql = rex_sql::factory();
        $array = array_merge([array('name'=>'---', 'id'=>$channel, 'parent_id'=>0)],$sql->getArray($query));
        $current = array();

        foreach ($array as $value) {
            if ($value['id'] == $id) {
                $current = $value;
                break;
            }
        }

        foreach ($array as $value) {
            $disable = ($id == $value['id'] or strpos($value['path'], $current['path']) !== false)
                ? array('disabled' => 'disabled')
                : array();
            $select->addOption($value['name'], $value['id'], $value['id'], 0, array_merge(array('data-level' => $value['level']), $disable));
        }

        $element->setLabel(ViewHelper::getLabel($item, 'label', $tableBaseName));
        $element->setAttribute('class', 'selectpicker form-control');

        if (array_key_exists('style', $item)) {
            $element->setAttribute('style', $item['style']);
        }

        return $element;
    }

    /**
     * @param rex_form $form
     * @param array $item
     * @param null $id
     * @param null $tableBaseName
     * @return mixed|rex_form_select_element
     * @throws rex_sql_exception
     * @author Joachim Doerr
     */
    public static function addCategoriesSelectElement(rex_form $form, array $item, $id = null, $tableBaseName = null)
    {
        $query = "SELECT name_".rex_clang::getCurrentId()." as name, id, parent FROM `".rex::getTablePrefix() . StoreChannelsActions::CATEGORIES_TABLE."`";

        $k = 't';
        $currentLevel = null;
        $currentParent = null;
        $name = 'name_' . rex_clang::getCurrentId();

        $query = "
            SELECT  CONCAT(REPEAT('--', level - 1), ' ', $k.$name) AS name,
                    category_sys_connect_by_path('/', $k.id) AS path,
                    parent, level, $k.id
            FROM    (
                    SELECT  category_connect_by_parent_eq_prior_id_with_level(id, 10) AS id,
                            CAST(@level AS SIGNED) AS level
                    FROM    (
                            SELECT  @start_with := 0,
                                    @id := @start_with,
                                    @level := 0
                            ) vars, ".rex::getTablePrefix() . StoreChannelsActions::CATEGORIES_TABLE."
                    WHERE   @id IS NOT NULL
                    ) ho
            JOIN    ".rex::getTablePrefix() . StoreChannelsActions::CATEGORIES_TABLE." $k
            ON      $k.id = ho.id
            ORDER BY path
        ";

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
            $select->addOption($sql->getValue('name'), $sql->getValue('id'), $sql->getValue('id')); //, $sql->getValue('parent'));
            $sql->next();
        }

        if ($form->isEditMode()) {
            $select->setSelected(explode(',', str_replace(array('[',']', '"'), '', $element->getValue())));
        }

        $element->setLabel(ViewHelper::getLabel($item, 'label', $tableBaseName));

        return $element;
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
        $attributes = array('internal::fieldClass' => 'rex_form_categories_prio_element');
        /** @var rex_form_categories_prio_element $element */
        $element = $form->addField('', $item['name'], null, $attributes, true);
        $element->setAttribute('class', 'selectpicker form-control');
        return $element;
    }
}