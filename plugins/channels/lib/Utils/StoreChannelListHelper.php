<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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