<?php

/**
 * User: joachimdoerr
 * Date: 24.01.17
 * Time: 11:41
 */
class StoreChannelActions
{
    const CHANNELS_TABLE = 'rex_store_channels';
    const CATEGORIES_TABLE = 'rex_store_categories';

    /**
     * @param array $params
     * @return string
     * @author Joachim Doerr
     */
    public static function createChannelCategory(array $params)
    {
        try {
            // load channel
            $sql = rex_sql::factory();
            $sql->setQuery("SELECT * FROM " . self::CHANNELS_TABLE . " AS sh WHERE id = {$params['id']}");
            $channel = $sql->getRow();
            // category exist?
            if (empty($channel['sh.category'])) {
                // get free id from categories
                $sql->setQuery("SELECT id FROM " . self::CATEGORIES_TABLE . " AS sc ORDER BY id DESC LIMIT 1");
                // first entry
                $id = (empty($sql->getRow())) ? 1 : $sql->getRow()['sc.id'] + 1; // set id from row
                // set category name
                $name = array();
                $nameValue = array();
                foreach (rex_clang::getAll() as $clang) {
                    $name[] = "`name_" . $clang->getId() . "`";
                    $nameValue[] = "\"" . $channel['sh.name'] . "\"";
                }
                // create category
                $sql->setQuery("INSERT INTO " . self::CATEGORIES_TABLE . " (`id`, " . implode(',', $name) . ", `parent`, `prio`, `status`) VALUES ($id, " . implode(',', $nameValue) . ", 0, {$channel['sh.prio']}, 1)");
                // set category id to channel
                $sql->setQuery('UPDATE ' . self::CHANNELS_TABLE . ' SET category = ' . $id . ' WHERE id = ' . $params['id']);
                // msg successful
                return rex_view::success(rex_i18n::msg('store_category_setcat_success'));
            } else {
                // redirect to category list
                header('Location: ' . rex_url::backendPage('store/channels/categories') . '&channel=' . $channel['sh.category']);
                return '';
            }
        } catch (\Exception $e) {
            rex_logger::logException($e);
            return rex_view::error(rex_i18n::msg('store_category_setcat_fail'));
        }
    }

    /**
     * @param array $params
     * @return string
     * @author Joachim Doerr
     */
    public static function deleteChannelCategory(array $params)
    {
        // get id
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT * FROM ' . self::CHANNELS_TABLE . ' AS sh WHERE id = ' . $params['id']);
        $channel = $sql->getRow();

        if (empty($channel['sh.category'])) {
            return rex_view::warning(rex_i18n::msg('store_category_cannot_remove_not_exist'));
        }

        // check use any category my category as parent?
        $sql->setQuery("SELECT * FROM " . self::CATEGORIES_TABLE . " WHERE parent = {$channel['sh.category']}");
        if ($sql->getRows() > 0) {
            return rex_view::error(rex_i18n::msg('store_category_catdelete_error_is_parent'));
        }

        // update channel remove id from chategory
        $sql->setQuery("UPDATE " . self::CHANNELS_TABLE . " SET category = NULL WHERE id = {$params['id']}");

        // delete category
        AlfredActionHelper::deleteData('rex_store_categories', $channel['sh.category']);

        return rex_view::info(rex_i18n::msg('store_category_catdelete_success'));
    }

    /**
     * @param array $params
     * @return string
     * @author Joachim Doerr
     */
    public static function onlineOfflineChannel(array $params)
    {
        $sql = rex_sql::factory();
        $sql->setQuery("SELECT * FROM " . self::CHANNELS_TABLE . " AS sh WHERE id = {$params['id']}");
        $channel = $sql->getRow();
        $msg = '';

        // set online offline channel
        if (AlfredActionHelper::toggleBoolData(self::CHANNELS_TABLE, $params['id'], 'status')) {
            $msg .= rex_view::info(rex_i18n::msg('store_channel_status_toggle_success'));
        } else {
            return rex_view::warning(rex_i18n::msg('store_channel_status_toggle_fail'));
        }

        if (!empty($channel['sh.category'])) {
            // set online offline category
            if (AlfredActionHelper::toggleBoolData('rex_store_categories', $params['id'], 'status')) {
                $msg .= rex_view::info(rex_i18n::msg('store_channel_category_status_toggle_success'));
            }
        }

        return $msg;
    }

    /**
     * @param array $params
     * @return string
     * @author Joachim Doerr
     */
    public static function deleteChannel(array $params)
    {
        try {
            // load channel
            $sql = rex_sql::factory();
            $sql->setQuery("SELECT * FROM " . self::CHANNELS_TABLE . " AS sh WHERE id = {$params['id']}");
            $channel = $sql->getRow();
            if (!empty($channel['sh.category'])) {
                // we cannot delete
                return rex_view::warning(rex_i18n::msg('store_channel_cannot_delete_because_category_binding'));
            } else {
                // we can delete
                AlfredActionHelper::deleteData(self::CHANNELS_TABLE, $params['id']);
                return rex_view::info(rex_i18n::msg('store_channel_delete_success'));
            }

        } catch (\Exception $e) {
            rex_logger::logException($e);
            return rex_view::error(rex_i18n::msg('store_channel_delete_fail'));
        }
    }

    /**
     * @param rex_extension_point $params
     * @author Joachim Doerr
     * @return string
     */
    public static function preSaveChannel(rex_extension_point $params)
    {
        $param = $params->getParams();
        /** @var rex_form $form */
        $form = $param['form'];

        // load changed data
        $sql = rex_sql::factory();
        $sql->setQuery("SELECT * FROM " . $form->getTableName() . " WHERE " . $form->getWhereCondition() . " LIMIT 1");
        $result = $sql->getRow();

        if (!empty($result[$form->getTableName() . '.category'])) {
            // category name
            $name = '';
            foreach (rex_clang::getAll() as $clang) {
                $name .= "name_" . $clang->getId() . " = \"" . $result[$form->getTableName() . '.name'] . "\", ";
            }
            // update category
            $sql->setQuery("UPDATE rex_store_categories SET $name prio = {$result[$form->getTableName().'.prio']}, status = {$result[$form->getTableName().'.status']} WHERE id = {$result[$form->getTableName().'.category']}");

            return rex_view::info(rex_i18n::msg('shop_channel_category_change_success'));
        }

        return '';
    }

}