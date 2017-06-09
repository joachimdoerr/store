<?php

/**
 * User: joachimdoerr
 * Date: 01.03.17
 * Time: 06:57
 */
class StoreCountriesListHelper
{
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
}