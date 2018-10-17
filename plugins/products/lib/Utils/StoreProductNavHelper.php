<?php

class StoreProductNavHelper
{
    /**
     * @param array $params
     * @return array
     * @author Joachim Doerr
     */
    public static function addStandingDataFormLink($params = array())
    {
        $pages = array_merge($params, array(
            'url_parameter' => array(
                'base_path' => rex_request::get('base_path', 'string'),
                'rows' => rex_request::get('rows', 'int'),
                'list_icon' => rex_request::get('list_icon', 'string'),
                'list' => rex_request::get('list', 'string'),
                'func' => 'edit',
                'id' => rex_request::get('id', 'int'),
                'start' => rex_request::get('start', 'int', ''),
                'sub_func' => '',
            ),
            'active_parameter' => '',
        ));

        // disable by func add and add by base edit
        if ((
                rex_request::get('func', 'string', '') == 'edit' &&
                rex_request::get('sub_func', 'string', '') == ''
            ) or (
                rex_request::get('func', 'string', '') == 'add'
            )
        ) {
            $pages['active'] = true;
            $pages['href'] = '#';
            $pages['link_class'] = 'disable';
        }

        return array($pages);
    }

    /**
     * @param array $params
     * @return array
     * @author Joachim Doerr
     */
    public static function addTestAdd($params = array())
    {
        $pages = array_merge($params, array(
            'url_parameter' => array(
                'base_path' => rex_request::get('base_path', 'string'),
                'rows' => rex_request::get('rows', 'int'),
                'list_icon' => rex_request::get('list_icon', 'string'),
                'list' => rex_request::get('list', 'string'),
                'func' => 'edit',
                'id' => rex_request::get('id', 'int'),
                'start' => rex_request::get('start', 'int', ''),
                'sub_func' => 'testadd',
            ),
            'active_parameter' => '',
        ));

        // active
        if (
            rex_request::get('func', 'string', '') == 'edit' &&
            rex_request::get('sub_func', 'string', '') == 'testadd'
        ) {
            $pages['active'] = true;
            $pages['href'] = '#';
            $pages['link_class'] = 'disable';
        }
        // disable by func add
        if (rex_request::get('func', 'string', '') == 'add') {
            $pages['href'] = '#';
            $pages['link_class'] = 'disable';
        }

        return array($pages);
    }
}