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
     */
    public static function formatZones($params)
    {
        /** @var rex_list $list */
        $list = $params["list"];

        if (!empty($list->getValue("zone"))) {
            $sql = rex_sql::factory();
            $sql->setQuery('SELECT * FROM rex_store_zones WHERE id=' . $list->getValue("zone"));
            $result = $sql->getArray();

            return $list->getColumnLink("zone", "<span>{$result[0]['name_en_gb']}</span>");
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