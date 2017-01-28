<?php

if (rex::isBackend() && rex::getUser()) {

    // add lang fields by clang_added event
    rex_extension::register('CLANG_ADDED', function () {
        // go for all
        $databaseManager = new AlfredDatabaseManager($this->getName());
        $databaseManager->executeCustomTablesLangHandling();
    });

    // register for replace default with cool headlines
    rex_extension::register(strtoupper($this->getName()) . '_TITLE', function ($params) {
        /** @var rex_extension_point $params */
        $headlineProvider = new AlfredHeadlineProvider();
        $headlineProvider->replaceDefaultHeadline($params);
    });

    // add navigation
    // TODO reset definitions
    $navigationProvider = new AlfredNavigationProvider($this->getName());
    $navigationProvider->addCustomNavigation();
}