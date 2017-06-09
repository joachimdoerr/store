<?php
/**
 * @author Joachim Doerr
 * @support www.dev51.com
 * @package shop.dev51
 */

//////////////////////////////
// add headlines to config
StoreHeadlineProvider::addPluginSiteHeadlinesToConfig($this->getAddon(), $this->getName());

//////////////////////////////
// create plugin database schema
$databaseManager = new StoreDatabaseManager($this->getAddon()->getName());
$databaseManager->executeCustomTablesHandling();
