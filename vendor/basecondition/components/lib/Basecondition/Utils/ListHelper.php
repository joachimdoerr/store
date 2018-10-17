<?php
/**
 * @package components
 * @author Joachim Doerr
 * @copyright (C) hello@basecondition.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Basecondition\Utils;


use rex_i18n;
use rex_list;

class ListHelper
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
            $str = $list->getColumnLink("status", "<span class=\"rex-online\"><i class=\"rex-icon rex-icon-online\"></i> " . rex_i18n::msg('clang_online') . "</span>");
        } else {
            $str = $list->getColumnLink("status", "<span class=\"rex-offline\"><i class=\"rex-icon rex-icon-offline\"></i> " . rex_i18n::msg('clang_offline') . "</span>");
        }
        return $str;
    }

    /**
     * @param rex_list $list
     * @param array $item
     * @param array $parameter
     * @param string $labelPrefix
     * @return rex_list
     * @author Joachim Doerr
     */
    public static function addFunctions(rex_list $list, array $item, array $parameter, $labelPrefix = '')
    {
        $label = true;
        $colspan = 0;

        $labelPrefix = (array_key_exists('default_label_prefix', $item)) ? $item['default_label_prefix'] : $labelPrefix;

        foreach (array('list_status', 'list_edit', 'list_delete', 'list_clone') as $value) {
            if (array_key_exists($value, $item)) {
                $colspan++;
            }
        }

        $th = "<th colspan=\"$colspan\">###VALUE###</th>";

        if (array_key_exists('list_status', $item) && $item['list_status'] == 1) {
            // Action Status
            self::setLabel($list, $item, 'status', $labelPrefix);
            $list->setColumnParams('status', array_merge($parameter, array('func' => 'status')));
            $list->setColumnLayout('status', array($th, '<td>###VALUE###</td>'));
            $list->setColumnFormat('status', 'custom', array('\Basecondition\Utils\ListHelper', 'formatStatus'));
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
                $list->addColumn($name, rex_i18n::msg($labelPrefix . '_' . $name));
                if ($label) {
                    self::setLabel($list, $item, $name, $labelPrefix);
                }
                if ($name == 'delete') {
                    $list->addLinkAttribute($name, 'data-confirm', rex_i18n::msg('confirm_delete'));
                }
                $list->setColumnParams($name, array_merge($parameter, array('func' => $name)));
                $list->setColumnLayout($name, array($th, '<td>###VALUE###</td>'));
                $list->setColumnFormat($name, 'custom', array('\Basecondition\Utils\ListHelper', 'addCustomLink'), array('name' => $name, 'icon' => $icon['icon'], 'icon_type' => $icon['icon_type'], 'msg' => rex_i18n::msg($name)));
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
        $prefix = (!empty($prefix)) ? $prefix . '_' : '';
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
     * @param rex_list|ListView $list
     * @param string $class
     * @param string $data
     * @return string
     * @author Joachim Doerr
     */
    public static function wrapList($message, $list, $class = 'base-list', $data = '')
    {
        return '<div class="'.$class.'" '.$data.'>' . $message . $list->show() . '</div>';
    }
}