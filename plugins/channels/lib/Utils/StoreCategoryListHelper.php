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
        return $list->getColumnLink("categories", "<span>###categories###</span>");
    }
}