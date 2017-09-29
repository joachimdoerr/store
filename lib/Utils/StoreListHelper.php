<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class StoreListHelper
{
    /**
     * toggle link on off
     * @param array $params
     * @return mixed
     * @author Joachim Doerr
     */
    public static function formatStatus($params)
    {
        /** @var rex_list $list */
        $list = $params["list"];

        if ($list->getValue("status") == 1) {
            $str = $list->getColumnLink("status", "<span class=\"rex-online\"><i class=\"rex-icon rex-icon-online\"></i> " . rex_i18n::msg('store_online') . "</span>");
        } else {
            $str = $list->getColumnLink("status", "<span class=\"rex-offline\"><i class=\"rex-icon rex-icon-offline\"></i> " . rex_i18n::msg('store_offline') . "</span>");
        }
        return $str;
    }

    /**
     * @param rex_list $list
     * @param array $item
     * @param array $parameter
     * @return rex_list
     * @author Joachim Doerr
     */
    public static function addFunctions(rex_list $list, array $item, array $parameter)
    {
        $label = true;
        $colspan = 0;
        $label_prefix = (array_key_exists('addon_key', $parameter)) ? $parameter['addon_key'] . '_' : 'store_';

        foreach (array('list_status', 'list_edit', 'list_delete', 'list_clone') as $value) {
            if (array_key_exists($value, $item)) {
                $colspan++;
            }
        }

        $th = "<th colspan=\"$colspan\">###VALUE###</th>";

        if (array_key_exists('list_status', $item) && $item['list_status'] == 1) {
            // Action Status
            self::setLabel($list, $item, 'status');
            $list->setColumnParams('status', array_merge($parameter, array('func' => 'status')));
            $list->setColumnLayout('status', array($th, '<td>###VALUE###</td>'));
            $list->setColumnFormat('status', 'custom', array('StoreListHelper', 'formatStatus'));
            $label = false;
            $th = '';
        }

        foreach (array(
                     'edit' => array('icon' => 'fa-pencil-square-o', 'icon_type' => 'rex-icon'),
                     'delete' => array('icon' => 'fa-trash', 'icon_type' => 'rex-offline'),
                     'clone' => array('icon' => 'fa-clone', 'icon_type' => 'rex-offline')
                 ) as $name => $icon)
            if (array_key_exists('list_' . $name, $item) && $item['list_' . $name] == 1) {
                // Action Edit
                $list->addColumn($name, rex_i18n::msg('store_' . $name));
                if ($label) {
                    self::setLabel($list, $item, $name, $label_prefix);
                }
                if ($name == 'delete') {
                    $list->addLinkAttribute($name, 'data-confirm', rex_i18n::msg('store_confirm_delete'));
                }
                $list->setColumnParams($name, array_merge($parameter, array('func' => $name)));
                $list->setColumnLayout($name, array($th, '<td>###VALUE###</td>'));
                $list->setColumnFormat($name, 'custom', array('StoreListHelper', 'addCustomLink'), array('name' => $name, 'icon' => $icon['icon'], 'icon_type' => $icon['icon_type'], 'msg' => rex_i18n::msg('store_' . $name)));
                $label = false;
                $th = '';
            }

        return $list;
    }

    /**
     * @param $params
     * @return string
     * @author Joachim Doerr
     */
    public static function addCustomLink($params)
    {
        /** @var rex_list $list */
        $list = $params['list'];

        if (!array_key_exists('icon_type', $params['params'])) {
            $params['params']['icon_type'] = 'rex-icon';
        }

        return $list->getColumnLink($params['params']['name'], "<span class=\"{$params['params']['icon_type']}\"><i class=\"rex-icon {$params['params']['icon']}\"></i> {$params['params']['msg']}</span>");
    }

    /**
     * @param rex_list $list
     * @param array $item
     * @param $name
     * @param string $prefix
     * @return mixed
     * @author Joachim Doerr
     */
    public static function setLabel(rex_list $list, array $item, $name, $prefix = '')
    {
        // set by all
        if (array_key_exists('label', $item)) {
            $list->setColumnLabel($name, rex_i18n::msg($item['label']));
            return $list;
        }
        $lang = explode('_', rex_i18n::getLocale());
        // set lang by clang
        foreach ($lang as $value) {
            $property = 'label_' . $value;
            if (array_key_exists($property, $item)) {
                $list->setColumnLabel($name, $item[$property]);
                return $list;
            }
        }
        $list->setColumnLabel($name, rex_i18n::msg($prefix . $item['name']));
        return $list;
    }

    /**
     * @param $message
     * @param rex_list|StoreListView $list
     * @return string
     * @author Joachim Doerr
     */
    public static function wrapList($message, $list)
    {
        return '<div class="store-list">' . $message . $list->show() . '</div>';
    }
}