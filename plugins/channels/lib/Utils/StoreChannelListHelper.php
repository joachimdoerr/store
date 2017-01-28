<?php

/**
 * User: joachimdoerr
 * Date: 21.01.17
 * Time: 17:27
 */
class StoreChannelListHelper
{
    public static function formatCategory($params)
    {
        /** @var rex_list $list */
        $list = $params["list"];
        if (empty($list->getValue("category"))) {
            $str = $list->getColumnLink("category", "<span class=\"rex-icon\"><i class=\"rex-icon fa-folder-o\"></i> " . rex_i18n::msg('store_category_setcat') . "</span>");
        } else {
            $str = $list->getColumnLink("category", "<span class=\"rex-icon\"><i class=\"rex-icon fa-folder\"></i> " . rex_i18n::msg('store_category_setcat_goto') . "</span>");
        }
        return $str;
    }
}