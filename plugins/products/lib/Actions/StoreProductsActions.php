<?php

/**
 * User: joachimdoerr
 * Date: 30.06.17
 * Time: 20:22
 */
class StoreProductsActions
{
    const PRODUCTS_TABLE = 'store_products';

    /**
     * @param array $params
     * @return string
     * @author Joachim Doerr
     */
    public static function delete(array $params)
    {
        // check use any category my category as parent?
//        $sql = rex_sql::factory();
//        $sql->setQuery("SELECT * FROM " . rex::getTablePrefix() . StoreChannelsActions::CATEGORIES_TABLE . " WHERE parent = {$params['id']}");

//        if ($sql->getRows() > 0)
//            return rex_view::error(rex_i18n::msg('store_category_delete_error_is_parent'));

        // check use any product my category?
//        $sql = rex_sql::factory();
//        $sql->setQuery("SEleCT * FROM" . StoreProduA::)

        // delete category
        ActionHelper::deleteData(rex::getTablePrefix() . self::PRODUCTS_TABLE, $params['id']);
        return rex_view::info(rex_i18n::msg('store_product_delete_success'));
    }

    /**
     * @param array $params
     * @return string
     * @author Joachim Doerr
     */
    public static function onlineOffline(array $params)
    {
        // set online offline channel
        if (ActionHelper::toggleBoolData(rex::getTablePrefix() . self::PRODUCTS_TABLE, $params['id'], 'status'))
            return rex_view::info(rex_i18n::msg('store_product_status_toggle_success'));
        else
            return rex_view::warning(rex_i18n::msg('store_product_status_toggle_fail'));
    }
}