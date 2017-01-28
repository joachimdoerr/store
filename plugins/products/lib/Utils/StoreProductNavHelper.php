<?php

class StoreProductNavHelper
{
    public static function addStandingDataFormLink($params = array())
    {
        $pages = array(
            'name' => 'testedit',
            'title_all' => 'Testedit',
            'url_parameter' => array('testedit' => 1),
            'active_parameter' => 'testedit',
            'id' => 123
        );

        if (array_key_exists('notonly', $params)) {
            $pages['notonly'] = $params['notonly'];
        }

        return array($pages);
    }

    public static function addTestAdd($params = array())
    {
        $pages['testadd'] = array(
            'name' => 'testadd',
            'title_all' => 'Testadd',
            'url_parameter' => array('testadd' => 1),
            'active_parameter' => 'testadd',
            'id' => 123
        );

        if (array_key_exists('notonly', $params)) {
            $pages['testadd']['notonly'] = $params['notonly'];
        }

        return $pages;
    }
}