<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class StorePluginEventListener
{
    /**
     * @param StorePluginActionEvent $event
     * @return StorePluginActionEvent
     * @author Joachim Doerr
     */
    static public function pluginInstall(StorePluginActionEvent $event)
    {
        // add cool headlines
        StoreHeadlineProvider::addPluginSiteHeadlinesToConfig($event->getAddon(), $event->getPlugin()->getName());

        // add database schema
        $databaseManager = new StoreDatabaseManager($event->getAddon()->getName());
        $databaseManager->executeCustomTablesHandling();

        return $event;
    }
}