<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Store\Listener;


use Basecondition\Database\DatabaseManager;
use rex_addon;
use rex_dir;
use rex_plugin;

class StorePluginEventListener
{
    /**
     * @param rex_plugin $plugin
     * @param rex_addon $addon
     * @param string|null $dataPath
     * @author Joachim Doerr
     */
    static public function pluginInstall(rex_plugin $plugin, rex_addon $addon, $dataPath = null)
    {
        // set data path
        $dataPath = (!is_null($dataPath)) ? str_replace('plugins',$dataPath, $plugin->getDataPath()) : $plugin->getDataPath();
        rex_dir::copy($plugin->getPath('data'), $dataPath); // copy data to data path

        // add cool headlines
        StoreHeadlineProvider::addPluginSiteHeadlinesToConfig($addon, $plugin->getName());

        // add database schema
        DatabaseManager::provideSchema($addon->getName(), $addon->getDataPath('resources'));
    }
}