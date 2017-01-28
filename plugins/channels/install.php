<?php
/**
 * @author Joachim Doerr
 * @support www.dev51.com
 * @package shop.dev51
 */

// add headlines to config
AlfredHeadlineProvider::addPluginSiteHeadlinesToConfig($this->getAddon()->getName(), $this->getName());

// create plugin database schema
$databaseManager = new AlfredDatabaseManager($this->getAddon()->getName());
$databaseManager->executeCustomTablesHandling();
