<?php

/**
 * User: joachimdoerr
 * Date: 25.12.16
 * Time: 01:41
 */
class StoreChannelNavHelper
{
    /**
     * @param array $params
     * @return array
     * @author Joachim Doerr
     */
    public static function addChannelsFilter(array $params = array())
    {
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT * FROM rex_store_categories AS sc WHERE sc.status = 1 AND sc.parent = 0 ORDER BY sc.prio');

        $pages = array();

        foreach ($sql->getArray() as $value) {
            $pages[] = array(
                'name' => $value['id'],
                'title_all' => $value['name_1'],
                'url_parameter' => array('channel' => $value['id']),
                'active_parameter' => 'channel',
                'id' => $value['id']
            );
        }

        return $pages;
    }

    /**
     * @param array $params
     * @return array
     * @author Joachim Doerr
     */
    public static function addProductsSearchFilter(array $params = array())
    {
//        echo 'TEST';
        return array();
    }

    /**
     * @param array $params
     * @return array
     * @author Joachim Doerr
     */
    public static function addProductsFilter(array $params = array())
    {
//        echo 'TEST';
        return array();
    }

    /**
     * @param array $params
     * @return null
     * @author Joachim Doerr
     */
    public static function getFirstChannelUrlParameter(array $params = array())
    {
        $channels = self::addChannelsFilter();

        if (sizeof($channels) > 0) {
            return $channels[0]['url_parameter'];
        } else {
            return array('channel_fail'=>1);
        }

        return null;
    }

}