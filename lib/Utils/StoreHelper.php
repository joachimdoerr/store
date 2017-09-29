<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class StoreHelper
 * TODO description
 */
class StoreHelper
{
    /**
     * @param $message
     * @return string
     * @author Joachim Doerr
     */
    public static function getPreFormMessage($message)
    {
        if (!empty($message)) {
            return '<div class="store-pre-form-msg">'.$message.'</div>';
        }
        return '';
    }

    /**
     * @param null $storePath
     * @return null|rex_plugin
     * @author Joachim Doerr
     */
    public static function getPluginByStorePath($storePath = null)
    {
        $p = self::getPByStorePath($storePath);
        $addon = self::getAddonByStorePath($storePath);
        return (sizeof($p) == 3 && $addon->pluginExists($p[1])) ? $addon->getPlugin($p[1]) : null;
    }

    /**
     * @param null $storePath
     * @return null|rex_addon
     * @author Joachim Doerr
     */
    public static function getAddonByStorePath($storePath = null)
    {
        $p = self::getPByStorePath($storePath);
        return (rex_addon::exists($p[0])) ? rex_addon::get($p[0]) : null;
    }

    /**
     * @param null $storePath
     * @return null
     * @author Joachim Doerr
     */
    public static function getSearchFileByStorePath($storePath = null)
    {
        $p = self::getPByStorePath($storePath);
        return array_pop($p);
    }

    /**
     * @param null $storePath
     * @return array
     * @author Joachim Doerr
     */
    private static function getPByStorePath($storePath = null)
    {
        if (is_null($storePath)) {
            $storePath = rex_request::get('store_path', 'string');
        }
        return explode('/', $storePath);
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
     * @return bool|mixed|string
     * @author Joachim Doerr
     */
    public static function getLabel(array $item, $type = 'label')
    {
        return self::getTranslatedStringByItem($item, $type);
    }

    /**
     * @param array $item
     * @return bool|mixed|string
     * @author Joachim Doerr
     */
    public static function getTitle(array $item)
    {
        return self::getTranslatedStringByItem($item, 'title');
    }

    /**
     * @param array $item
     * @param string $type
     * @return mixed|string
     * @author Joachim Doerr
     */
    public static function getTranslatedStringByItem(array $item, $type)
    {
        // set by all
        if (array_key_exists($type . '_all', $item))
            return $item[$type . '_all'];

        $lang = explode('_',rex_i18n::getLocale());
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
            return rex_i18n::msg($item['name']);
        }
        return '';
    }

    /**
     * @param $arr
     * @param $col
     * @param int $dir
     * @author Joachim Doerr
     */
    public static function arraySortByColumn(&$arr, $col, $dir = SORT_ASC)
    {
        $sortCol = array();
        foreach ($arr as $key => $row) {
            if (is_array($row) && !array_key_exists($col, $row)) {
                $row[$col] = NULL;
            }
            $sortCol[$key] = $row[$col];
        }
        array_multisort($sortCol, $dir, $arr);
    }
}