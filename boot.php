<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


use Basecondition\Database\DatabaseManager;
use Basecondition\Navigation\NavigationProvider;
use Basecondition\Utils\MBlockHelper;
use Store\Listener\PluginEventListener;
use Store\Provider\HeadlineProvider;


// add listener
rex_extension::register('STORE_PLUGIN_INSTALL', function (rex_extension_point $params) {
    PluginEventListener::pluginInstall($params->getParam('plugin'), $params->getParam('addon'), $params->getParam('data_path'));
});


// is backend
if (rex::isBackend() && rex::getUser()) {

    // add assets
    rex_view::addJSFile($this->getAssetsUrl('js/' . $this->getName() . '.js'));
    rex_view::addCssFile($this->getAssetsUrl('css/' . $this->getName() . '.css'));

    // mblock form part for ajax call
    if (rex_request::get('add_mblock_block', 'int', 0)) {
        // open output buffer
        rex_response::cleanOutputBuffers();
        // print form part
        print MBlockHelper::getAddMBlock();
        exit();
    }

    // add lang fields by clang_added event
    rex_extension::register('CLANG_ADDED', function () {
        DatabaseManager::provideLangSchema($this->getName(), $this->getDataPath('resources'));
    });

    // register for replace default- with cool-headlines
    rex_extension::register('STORE_TITLE', function (rex_extension_point $params) {
        // read headline by config
        $headline = HeadlineProvider::getPluginSiteHeadlinesByConfig($this->getAddon());
        /** @var rex_extension_point $params */
        $headlineProvider = new HeadlineProvider();
        $headlineProvider->replaceDefaultHeadline($params, $headline['title'], $headline['subtitle'], $headline['icon'], $headline['replace']);
    });

    // add navigation
    NavigationProvider::manipulateNavigation($this->getName(), 'store/list_form', $this->getDataPath('resources'));


    // add database schema
    DatabaseManager::provideSchema($this->getName(), $this->getDataPath('resources'));

}

###
# Register EP
# $login_status = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGIN_FAILED', $login_status, array(
#'login_name' => $login_name, 'login_psw' => $login_psw, 'login_stay' => $login_stay, 'logout' => $logout, 'query_extras' => $query_extras)));
###
