<?php
/**
 * @author Joachim Doerr
 * @copyright Joachim Doerr
 * @package redaxo5 / store.dev51
 *
 * @license http://store.dev51.com/license
 * @support http://storesupport.dev51.com
 */

// add redactor store product profiles
if (rex_addon::exists('redactor2') && rex_addon::get('redactor2')->isAvailable()) {
    if (!redactor2::profileExists('store_product_teaser')) {
        redactor2::insertProfile('store_product_teaser', 'store product teaser config', 150, 250, 'relative', 'blockquote,bold,italic,underline,deleted,cleaner,fontsize[100%|120%|140%],grouplink[email|external|internal|media]');
    }
    if (!redactor2::profileExists('store_product_text')) {
        redactor2::insertProfile('store_product_text', 'store product text config', 300, 800, 'relative', 'groupheading[2|3|4|5],unorderedlist,alignment,blockquote,bold,italic,underline,deleted,cleaner,fontsize[100%|120%|140%],grouplink[email|external|internal|media],horizontalrule,fullscreen');
    }
}

// add headlines to config
AlfredHeadlineProvider::addPluginSiteHeadlinesToConfig($this->getAddon()->getName(), $this->getName());

// create plugin database schema
$databaseManager = new AlfredDatabaseManager($this->getAddon()->getName());
$databaseManager->executeCustomTablesHandling();