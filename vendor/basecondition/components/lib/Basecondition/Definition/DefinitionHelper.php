<?php
/**
 * @package components
 * @author Joachim Doerr
 * @copyright (C) hello@basecondition.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Basecondition\Definition;


use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use rex_i18n;

class DefinitionHelper
{
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