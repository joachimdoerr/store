<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


//////////////////////////////
// init event dispatcher
$event = new StoreEvent();
$this->setProperty('eventDispatcher', $event);

//////////////////////////////
// add listener
StoreEvent::addListener('store_plugin_install', function ($event) {
    StorePluginEventListener::pluginInstall($event);
});
StoreEvent::addListener('store_func_action', function($event) {
//    StoreRexFormValidationEventListener::executeFuncAction($event);
});

//////////////////////////////
// is backend
if (rex::isBackend() && rex::getUser()) {

    //////////////////////////////
    // add assets
    rex_view::addJSFile($this->getAssetsUrl('js/' . $this->getName() . '.js'));
    rex_view::addCssFile($this->getAssetsUrl('css/' . $this->getName() . '.css'));

    //////////////////////////////
    // mblock form part for ajax call
    if (rex_request::get('add_store_mblock_block', 'int', 0)) {
        // open output buffer
        rex_response::cleanOutputBuffers();
        // print form part
        print StoreMBlockHelper::getAddMBlock();
        exit();
    }

    //////////////////////////////
    // add lang fields by clang_added event
    rex_extension::register('CLANG_ADDED', function () {
        // go for all
        $databaseManager = new StoreDatabaseManager($this->getName());
        $databaseManager->executeCustomTablesLangHandling();
    });

    //////////////////////////////
    // register for replace default- with cool-headlines
    rex_extension::register('STORE_TITLE', function (rex_extension_point $params) {
        // read headline by config
        $headline = StoreHeadlineProvider::getPluginSiteHeadlinesByConfig($this->getAddon());
        /** @var rex_extension_point $params */
        $headlineProvider = new StoreHeadlineProvider();
        $headlineProvider->replaceDefaultHeadline($params, $headline['title'], $headline['subtitle'], $headline['icon'], $headline['replace']);
    });

    //////////////////////////////
    // add navigation
    // TODO reset definitions
    $navigationProvider = new StoreNavigationProvider($this->getName());
    $navigationProvider->addCustomNavigation();
}

###
# Register EP
# $login_status = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGIN_FAILED', $login_status, array(
#'login_name' => $login_name, 'login_psw' => $login_psw, 'login_stay' => $login_stay, 'logout' => $logout, 'query_extras' => $query_extras)));
###

//    # create database TEST TODO remove
//    $definition = new StoreDefinitionManager($this->getAddon()->getName(), "*/default/products.yml");
//    $definition->createDefinition();
//    echo '<pre>';
//    print_r($definition->getDefinition());
//    die;
//    $databaseManager = new StoreDatabaseManager($this->getAddon()->getName());
//    $databaseManager = new StoreDatabaseManager($this->getAddon()->getName(),  "*/default/categories.yml");
//    $databaseManager = new StoreDatabaseManager($this->getAddon()->getName(),  "*/default/channels.yml");
//    $databaseManager->executeCustomTablesHandling();