<?php

/**
 * User: joachimdoerr
 * Date: 19.12.16
 * Time: 17:34
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
        $str = '';
//        if ($list->getValue("status") == 1) {
//            $str = $list->getColumnLink("status", "<span class=\"rex-online\"><i class=\"rex-icon rex-icon-online\"></i> " . rex_i18n::msg('shop_online') . "</span>");
//        } else {
//            $str = $list->getColumnLink("status", "<span class=\"rex-offline\"><i class=\"rex-icon rex-icon-offline\"></i> " . rex_i18n::msg('shop_offline') . "</span>");
//        }

        return $list->getColumnLink("categories", "<span style=\"color:###color###\">###categories###</span>");

//        return $str;
    }
}