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


use Basecondition\Definition\DefinitionHelper;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use rex_addon;
use rex_i18n;
use rex_list;
use rex_plugin;
use rex_request;

class ViewHelper
{
    /**
     * @param $arr
     * @param $col
     * @param int $dir
     * @author Joachim Doerr
     */
    public static function arraySortByColumn(&$arr, $col, $dir = SORT_ASC)
    {
       DefinitionHelper::arraySortByColumn($arr, $col, $dir);
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
     * @param string $class
     * @param $data
     * @return string
     * @author Joachim Doerr
     */
    public static function getPreFormMessage($message, $class = "bsc-pre-form-msg", $data)
    {
        if (!empty($message)) {
            return '<div class="' . $class . '" ' . $data . '>'.$message.'</div>';
        }
        return '';
    }

    /**
     * @param null $basePath
     * @return null|rex_plugin
     * @author Joachim Doerr
     */
    public static function getPluginByBasePath($basePath = null)
    {
        $p = self::getPByBasePath($basePath);
        $addon = self::getAddonByBasePath($basePath);
        return (sizeof($p) == 3 && $addon->pluginExists($p[1])) ? $addon->getPlugin($p[1]) : null;
    }

    /**
     * @param null $basePath
     * @return null|rex_addon
     * @author Joachim Doerr
     */
    public static function getAddonByBasePath($basePath = null)
    {
        $p = self::getPByBasePath($basePath);
        return (rex_addon::exists($p[0])) ? rex_addon::get($p[0]) : null;
    }

    /**
     * @param null $basePath
     * @return null
     * @author Joachim Doerr
     */
    public static function getSearchFileByBasePath($basePath = null)
    {
        $p = self::getPByBasePath($basePath);
        return array_pop($p);
    }

    /**
     * @param null $basePath
     * @return array
     * @author Joachim Doerr
     */
    private static function getPByBasePath($basePath = null)
    {
        if (is_null($basePath)) {
            $basePath = rex_request::get('base_path', 'string');
        }
        return explode('/', $basePath);
    }

    /**
     * @param $tableName
     * @return string
     * @author Joachim Doerr
     */
    public static function getTableKey($tableName)
    {
        return substr($tableName, 0, 2);
    }

    /**
     * @param array $array
     * @return int
     * @author Joachim Doerr
     */
    public static function getArrayDepth(array $array) {
        $depth = 0;
        $iteIte = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));

        foreach ($iteIte as $ite) {
            $d = $iteIte->getDepth();
            $depth = $d > $depth ? $d : $depth;
        }
        return $depth;
    }

    /**
     * @param array $item
     * @param string $type
     * @param string $tableBaseName
     * @return bool|mixed|string
     * @author Joachim Doerr
     */
    public static function getLabel(array $item, $type = 'label', $tableBaseName = '')
    {
        return self::getTranslatedStringByItem($item, $type, $tableBaseName);
    }

    /**
     * @param array $item
     * @param string $tableBaseName
     * @return bool|mixed|string
     * @author Joachim Doerr
     */
    public static function getTitle(array $item, $tableBaseName = '')
    {
        return self::getTranslatedStringByItem($item, 'title', $tableBaseName);
    }

    /**
     * @param array $item
     * @param string $type
     * @param string $table
     * @return mixed|string
     * @author Joachim Doerr
     */
    public static function getTranslatedStringByItem(array $item, $type, $table = '')
    {
        // set by all
        if (array_key_exists($type . '_all', $item))
            return $item[$type . '_all'];

        $lang = explode('_',rex_i18n::getLocale());
        $table = (!empty($table)) ? $table . '_' : '';

        // set lang by clang
        foreach ($lang as $value) {
            $property = $type . '_' . $value;
            if (array_key_exists($property, $item)) {
                return $item[$property];
            }
        }
        if (array_key_exists($type, $item)) {
            return rex_i18n::msg($item[$type]);
        }
        if (array_key_exists('name', $item)) {
            return rex_i18n::msg($table . $item['name']);
        }
        return '';
    }
}