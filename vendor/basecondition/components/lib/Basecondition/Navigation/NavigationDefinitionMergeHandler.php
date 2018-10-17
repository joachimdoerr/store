<?php
/**
 * @package components
 * @author Joachim Doerr
 * @copyright (C) hello@basecondition.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Basecondition\Navigation;


use Basecondition\Definition\DefinitionMergeInterface;
use Basecondition\Definition\DefinitionHelper;

class NavigationDefinitionMergeHandler implements DefinitionMergeInterface
{
    /**
     * @param array $array
     * @author Joachim Doerr
     * @return array
     */
    public static function merge(array $array)
    {
        $definition = call_user_func_array('array_replace_recursive', $array);

        if (is_array($definition)) {
            // sort by position
            DefinitionHelper::arraySortByColumn($definition, 'position');

            // sort all pages and subpages
            foreach ($definition as $key => $definitionItem) {
                if (is_array($definitionItem) && array_key_exists('pages', $definitionItem)) {
                    // sort by position too
                    DefinitionHelper::arraySortByColumn($definition[$key]['pages'], 'position');

                    // each all pages
                    foreach ($definition[$key]['pages'] as $pk => $page) {
                        foreach (array('subpages', 'subpages_edit', 'subpages_add', 'subpages_form', 'subpages_list') as $val) { // sort for all sub of sub page lists
                            if (is_array($page) && array_key_exists($val, $page)) // if sub of sub page exist?
                                DefinitionHelper::arraySortByColumn($definition[$key]['pages'][$pk][$val], 'position'); // sort it...
                        }
                    }
                }
            }
        }

        return $definition;
    }
}