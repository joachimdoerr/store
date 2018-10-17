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


use Basecondition\Definition\DefinitionProvider;
use rex_addon;

class NavigationProvider
{
    const CACHE_PATH = '*/definitions';
    const SEARCH_SCHEMA = '*/definitions/*navigation.yml';

    /**
     * @param $addonName
     * @param $basePath
     * @param $searchPath
     * @param string $searchSchema
     * @author Joachim Doerr
     */
    public static function manipulateNavigation($addonName, $basePath, $searchPath, $searchSchema = self::SEARCH_SCHEMA)
    {
        // TODO check addon exist
        $addon = rex_addon::get($addonName);
        $basePath = str_replace('//', '/', $basePath . '/');
        $definition = self::getNavigationDefinition($addonName, $searchPath, $searchSchema);
        $navigationManipulator = new NavigationManipulator($addonName, $basePath . '/');

        if (array_key_exists('data', $definition) && is_array($definition['data']))
            $navigationManipulator->displayCustomNavigation($definition['data']); // add navigation
    }

    /**
     * @param string $searchPath
     * @param string $searchSchema
     * @param string $addonName
     * @return false|mixed
     * @author Joachim Doerr
     */
    public static function getNavigationDefinition($addonName, $searchPath, $searchSchema = self::SEARCH_SCHEMA)
    {
        // TODO check addon exist
        $addon = rex_addon::get($addonName);
        $searchPath = str_replace('//', '/', $searchPath . '/');
        return DefinitionProvider::load($searchSchema, $searchPath, $addon->getCachePath(), new NavigationDefinitionMergeHandler, true);
    }
}