<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class StoreChannelNavHelper
{
    /**
     * @param array $params
     * @return array
     * @author Joachim Doerr
     * @throws rex_sql_exception
     */
    public static function addChannelsFilter(array $params = array())
    {
        $sql = rex_sql::factory();
        $sql->setQuery("SELECT * FROM ".rex::getTablePrefix() . StoreChannelsActions::CHANNELS_TABLE." AS sc WHERE sc.status = 1 AND sc.category IS NOT NULL ORDER BY sc.prio");
        $pages = array();
        foreach ($sql->getArray() as $value) {
            $pages[] = array(
                'name' => $value['category'],
                'title' => $value['name'],
                'url_parameter' => array('channel' => $value['category']),
                'active_parameter' => 'channel',
                'id' => $value['category']
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
     * @return mixed
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
    }
}