<?php

/**
 * User: joachimdoerr
 * Date: 30.01.17
 * Time: 08:16
 */
class StoreCategoriesActions
{
    /**
     * @param array $params
     * @return string
     * @author Joachim Doerr
     */
    public static function deleteCategory(array $params)
    {
        // check use any category my category as parent?
        $sql = rex_sql::factory();
        $sql->setQuery("SELECT * FROM " . StoreChannelsActions::CATEGORIES_TABLE . " WHERE parent = {$params['id']}");

        if ($sql->getRows() > 0)
            return rex_view::error(rex_i18n::msg('store_category_delete_error_is_parent'));

        // delete category
        StoreActionHelper::deleteData(StoreChannelsActions::CATEGORIES_TABLE, $params['id']);
        return rex_view::info(rex_i18n::msg('store_category_delete_success'));
    }

    /**
     * @param array $params
     * @return string
     * @author Joachim Doerr
     */
    public static function onlineOfflineCategory(array $params)
    {
        // set online offline channel
        if (StoreActionHelper::toggleBoolData(StoreChannelsActions::CATEGORIES_TABLE, $params['id'], 'status'))
            return rex_view::info(rex_i18n::msg('store_channel_status_toggle_success'));
        else
            return rex_view::warning(rex_i18n::msg('store_channel_status_toggle_fail'));
    }
}