<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class StoreCategoryListHelper
{
    public static function addId()
    {
    }

    public static function formatCategories($params)
    {
        /** @var rex_list $list */
        $list = $params["list"];

        if (!empty($list->getValue("categories"))) {

            $ids = array();
            $names = array();

            try {
                $json = json_decode($list->getValue("categories"));
                if (is_array($json)) {
                    $ids = $json;
                } else {
                    $ids[] = $list->getValue("categories");
                }
            } catch (\Exception $e) {
                $ids[] = $list->getValue("categories");
            }

            if (sizeof($ids) > 0) {
                $sql = rex_sql::factory();
                $sql->setQuery('SELECT * FROM rex_store_categories WHERE id IN ("'. implode('","', $ids) .'")');
                $result = $sql->getArray();

                if (sizeof($result) > 0) {
                    foreach ($result as $item) {
                        if (array_key_exists('name_1', $item)) {
                            $names[] = $item['name_1'];
                        }
                    }
                }
            }

            return $list->getColumnLink("categories", "<span>".implode(', ', $names)."</span>");
        }
        return '';


//        return $list->getColumnLink("categories", "<span>###categories###</span>");
    }
}