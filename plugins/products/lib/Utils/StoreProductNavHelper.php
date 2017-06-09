<?php

class StoreProductNavHelper
{
    public static function addStandingDataFormLink($params = array())
    {
        $pages = array(
            'name' => $params['name'],
            'title' => $params['name'],
            'url_parameter' => array('func' => 'edit'),
            'active_parameter' => 'testedit',
            'id' => 123,
            'params' => $params,
            'icon' => (isset($params['icon'])) ? $params['icon'] : '',
            'class' => (isset($params['class'])) ? $params['class'] : '',
        );

        if (rex_request::get('func', 'string', '') == 'edit' && rex_request::get('sub_func', 'string', '') == '') {
            $pages['active'] = true;
            $pages['href'] = '#';
        }

        #http://nihonto/redaxo/index.php?page=store/products/products&testedit=1

        if (array_key_exists('notonly', $params)) {
            $pages['notonly'] = $params['notonly'];
        }

        return array($pages);
    }

    public static function addTestAdd($params = array())
    {
        $pages = array(
            'name' => $params['name'],
            'title' => $params['name'],
            'url_parameter' => array('testadd' => 1),
            'active_parameter' => 'testadd',
            'id' => 123,
            'params' => $params,
            'icon' => (isset($params['icon'])) ? $params['icon'] : '',
            'class' => (isset($params['class'])) ? $params['class'] : '',
        );

        if (rex_request::get('func', 'string', '') == 'add' && rex_request::get('sub_func', 'string', '') == '') {
            $pages['active'] = true;
            $pages['href'] = '#';
        }

        if (array_key_exists('notonly', $params)) {
            $pages['notonly'] = $params['notonly'];
        }

        return array($pages);
    }

    public static function addTestAdd2($params = array())
    {
        $pages['testadd'] = array(
            'name' => $params['name'],
            'title' => $params['name'],
            'url_parameter' => array('testadd2' => 1),
            'active_parameter' => 'testadd2',
            'id' => 1232,
            'params' => $params,
            'icon' => (isset($params['icon'])) ? $params['icon'] : '',
            'class' => (isset($params['class'])) ? $params['class'] : '',
        );

        if (array_key_exists('notonly', $params)) {
            $pages['testadd']['notonly'] = $params['notonly'];
        }

        return $pages;
    }
}