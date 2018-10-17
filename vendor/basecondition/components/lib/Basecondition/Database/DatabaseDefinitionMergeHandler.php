<?php
/**
 * @package components
 * @author Joachim Doerr
 * @copyright (C) hello@basecondition.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Basecondition\Database;


use Basecondition\Definition\DefinitionMergeInterface;
use Basecondition\Definition\DefinitionHelper;

class DatabaseDefinitionMergeHandler implements DefinitionMergeInterface
{
    /**
     * @param array $array
     * @return array
     * @author Joachim Doerr
     */
    public static function merge(array $array)
    {
        $definition = call_user_func_array('array_merge_recursive', call_user_func_array('array_merge', $array));

        // sort lang and panel
        if (is_array($definition)) {
            foreach ($definition as $key => $value) {
                if (strpos($key, 'lang') !== false ||
                    strpos($key, 'panel') !== false
                ) {
                    // merge it
                    $definition[$key] = call_user_func_array('array_merge_recursive', $value);
                    // panel in lang
                    if (is_array($definition[$key])) {
                        foreach ($definition[$key] as $k => $v) {
                            if (strpos($k, 'panel') !== false) {
                                // merge it
                                $definition[$key][$k] = call_user_func_array('array_merge_recursive', $v);

                                // sort fields
                                foreach ($definition[$key][$k] as $kv => $vv) {
                                    if (strpos($kv, 'fields') !== false) {
                                        // sort it
                                        DefinitionHelper::arraySortByColumn($definition[$key][$k][$kv], 'prio');
                                    }
                                }
                            } else if (strpos($k, 'field') !== false) {
                                if (is_array($v)) {
                                    foreach ($v as $kk => $vk) {
                                        if (is_array($vk) && array_key_exists('field_prio', $vk)) {
                                            $definition[$key][$k]['field_prio'] = $vk['field_prio'];
                                            unset($definition[$key][$k][$kk]);
                                        }
                                    }
                                    // sort it
                                    DefinitionHelper::arraySortByColumn($definition[$key][$k], 'prio');
                                }
                            }
                        }
                    }
                }
                if (strpos($key, 'fields') !== false) {
                    if (is_array($value)) {
                        // sort it
                        foreach ($value as $k => $v) {
                            if (array_key_exists('field_prio', $v)) {
                                $definition[$key]['field_prio'] = $v['field_prio'];
                                unset($definition[$key][$k]);
                            }
                        }
                        // sort it
                        DefinitionHelper::arraySortByColumn($definition[$key], 'prio');
                    }
                }
                // sort it
                # DefinitionHelper::arraySortByColumn($definition[$key], 'field_prio');
            }
            // sort it
            DefinitionHelper::arraySortByColumn($definition, 'field_prio');
        }

        return $definition;
    }
}