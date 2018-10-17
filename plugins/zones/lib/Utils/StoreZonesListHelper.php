<?php

/**
 * User: joachimdoerr
 * Date: 01.03.17
 * Time: 06:57
 */
class StoreZonesListHelper
{
    /**
     * @param $params
     * @return string
     * @author Joachim Doerr
     */
    public static function formatEuCountry($params)
    {
        /** @var rex_list $list */
        $list = $params["list"];
        if (!empty($list->getValue("is_eu"))) {
            $str = $list->getColumnLink("is_eu", "<span class=\"rex-icon\"><i class=\"rex-icon fa-check-square-o\"></i></span>");
        } else {
            $str = $list->getColumnLink("is_eu", "");
        }
        return $str;
    }

    /**
     * @param $params
     * @return string
     * @author Joachim Doerr
     * @throws rex_sql_exception
     */
    public static function formatZones($params)
    {
        /** @var rex_list $list */
        $list = $params["list"];

        if (!empty($list->getValue("zone"))) {

            $ids = array();
            $names = array();

            try {
                $json = json_decode($list->getValue("zone"));
                if (is_array($json)) {
                    $ids = $json;
                } else {
                    $ids[] = $list->getValue("zone");
                }
            } catch (\Exception $e) {
                $ids[] = $list->getValue("zone");
            }

            if (sizeof($ids) > 0) {
                $sql = rex_sql::factory();
                $sql->setQuery('SELECT * FROM rex_store_zones WHERE id IN ("'. implode('","', $ids) .'")');
                $result = $sql->getArray();

                if (sizeof($result) > 0) {
                    foreach ($result as $item) {
                        if (array_key_exists('name_en_gb', $item)) {
                            $names[] = $item['name_en_gb'];
                        }
                    }
                }
            }

            return $list->getColumnLink("zone", "<span>".implode(', ', $names)."</span>");
        }
        return '';
    }

    /**
     * @param $params
     * @return string
     * @author Joachim Doerr
     */
    public static function formatCountry($params)
    {
        /** @var rex_list $list */
        $list = $params["list"];

        if (!empty($list->getValue("country"))) {
            $sql = rex_sql::factory();
            $sql->setQuery('SELECT * FROM rex_store_countries WHERE id=' . $list->getValue("country"));
            $result = $sql->getArray();

            return $list->getColumnLink("country", "<span>{$result[0]['name_en_gb']}</span>");
        }
        return '';
    }

}